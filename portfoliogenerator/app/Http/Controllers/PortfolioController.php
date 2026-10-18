<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    private const TEMPLATES = ['simple', 'modern', 'creative'];

    public function index(): View
    {
        return view('home');
    }

    public function create(): View
    {
        return view('portfolios.form', ['portfolio' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedPortfolio($request);
        $data['uuid'] = (string) Str::uuid();
        $data['management_token'] = Str::random(64);
        $data['user_id'] = $request->user()?->id;

        $portfolio = Portfolio::create($data);

        return redirect()->route('portfolios.templates', $portfolio->management_token);
    }

    public function templates(string $managementToken): View
    {
        $portfolio = $this->findManagedPortfolio($managementToken);

        return view('portfolios.templates', compact('portfolio'));
    }

    public function selectTemplate(Request $request, string $managementToken): RedirectResponse
    {
        $portfolio = $this->findManagedPortfolio($managementToken);
        $data = $request->validate([
            'template' => ['required', 'in:'.implode(',', self::TEMPLATES)],
        ]);

        $portfolio->update($data);

        return redirect()->route('portfolios.manage', $portfolio->management_token);
    }

    public function show(Portfolio $portfolio): View
    {
        return view('portfolios.show', compact('portfolio'));
    }

    public function manage(string $managementToken): View
    {
        $portfolio = $this->findManagedPortfolio($managementToken);

        return view('portfolios.manage', compact('portfolio'));
    }

    public function edit(string $managementToken): View
    {
        $portfolio = $this->findManagedPortfolio($managementToken);

        return view('portfolios.form', compact('portfolio'));
    }

    public function update(Request $request, string $managementToken): RedirectResponse
    {
        $portfolio = $this->findManagedPortfolio($managementToken);
        $data = $this->validatedPortfolio($request, $portfolio);

        if (filled($data['profile_image'] ?? null) && ! $portfolio->isRemoteProfileImage()) {
            Storage::disk('public')->delete($portfolio->profile_image);
        }

        $portfolio->update($data);

        return redirect()->route('portfolios.manage', $portfolio->management_token)->with('status', 'Your portfolio has been updated.');
    }

    public function destroy(string $managementToken): RedirectResponse
    {
        $portfolio = $this->findManagedPortfolio($managementToken);

        if ($portfolio->profile_image && ! $portfolio->isRemoteProfileImage()) {
            Storage::disk('public')->delete($portfolio->profile_image);
        }

        $portfolio->delete();

        return redirect()->route('home')->with('status', 'Your portfolio has been deleted.');
    }

    private function findManagedPortfolio(string $managementToken): Portfolio
    {
        return Portfolio::where('management_token', $managementToken)->firstOrFail();
    }

    private function validatedPortfolio(Request $request, ?Portfolio $portfolio = null): array
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'profile_image' => ['nullable', 'image', 'max:4096'],
            'profile_image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'about_me' => ['nullable', 'string', 'max:5000'],
            'education' => ['nullable', 'array'],
            'education.*.qualification' => ['nullable', 'string', 'max:160'],
            'education.*.institution' => ['nullable', 'string', 'max:160'],
            'education.*.year' => ['nullable', 'string', 'max:40'],
            'education.*.details' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable', 'string', 'max:80'],
            'projects' => ['nullable', 'array'],
            'projects.*.name' => ['nullable', 'string', 'max:160'],
            'projects.*.description' => ['nullable', 'string', 'max:2000'],
            'projects.*.url' => ['nullable', 'url:http,https', 'max:2048'],
            'experience' => ['nullable', 'array'],
            'experience.*.role' => ['nullable', 'string', 'max:160'],
            'experience.*.company' => ['nullable', 'string', 'max:160'],
            'experience.*.period' => ['nullable', 'string', 'max:80'],
            'experience.*.details' => ['nullable', 'string', 'max:2000'],
            'links' => ['nullable', 'array'],
            'links.*.label' => ['nullable', 'string', 'max:80'],
            'links.*.url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        foreach (['education', 'projects', 'experience', 'links'] as $group) {
            $data[$group] = array_values(array_filter(
                $data[$group] ?? [],
                fn (array $row): bool => collect($row)->contains(fn ($value): bool => filled($value)),
            ));
        }

        $data['skills'] = array_values(array_filter($data['skills'] ?? [], fn ($skill): bool => filled($skill)));

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->storePublicly('portfolio-profiles', 'public');
        } elseif (filled($data['profile_image_url'] ?? null)) {
            $data['profile_image'] = $data['profile_image_url'];
        } elseif (! $portfolio) {
            $data['profile_image'] = null;
        }

        unset($data['profile_image_url']);

        return $data;
    }
}

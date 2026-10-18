<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_https_proxy_headers_are_used_for_asset_urls(): void
    {
        $this->withServerVariables(['HTTP_X_FORWARDED_PROTO' => 'https'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('https://localhost/css/portfolio.css', false)
            ->assertSee('https://localhost/js/portfolio.js', false);
    }

    public function test_a_portfolio_can_be_created_styled_viewed_edited_and_deleted(): void
    {
        $response = $this->post(route('portfolios.store'), [
            'full_name' => 'Morgan Chen',
            'profile_image_url' => 'https://images.example.com/morgan.jpg',
            'email' => 'morgan@example.com',
            'contact_number' => '+1 555 0100',
            'address' => 'Toronto, Canada',
            'about_me' => 'I make useful things with thoughtful teams.',
            'education' => [[
                'qualification' => 'BDes',
                'institution' => 'North College',
                'year' => '2022–2026',
                'details' => 'Communication design',
            ]],
            'skills' => ['Research', 'Prototyping'],
            'projects' => [[
                'name' => 'Field Notes',
                'description' => 'A community resource directory.',
                'url' => 'https://example.com/field-notes',
            ]],
            'experience' => [[
                'role' => 'Design intern',
                'company' => 'Common Ground',
                'period' => '2025',
                'details' => 'Worked across research and product.',
            ]],
            'links' => [[
                'label' => 'LinkedIn',
                'url' => 'https://linkedin.com/in/morgan',
            ]],
        ]);

        $portfolio = Portfolio::firstOrFail();
        $response->assertRedirect(route('portfolios.templates', $portfolio->management_token));
        $this->assertDatabaseHas('portfolios', [
            'uuid' => $portfolio->uuid,
            'profile_image' => 'https://images.example.com/morgan.jpg',
            'full_name' => 'Morgan Chen',
            'email' => 'morgan@example.com',
        ]);
        $this->assertSame(['Research', 'Prototyping'], $portfolio->skills);

        $this->get(route('portfolios.templates', $portfolio->management_token))
            ->assertOk()
            ->assertSee('Simple')
            ->assertSee('Modern')
            ->assertSee('Creative');

        $this->patch(route('portfolios.template', $portfolio->management_token), ['template' => 'creative'])
            ->assertRedirect(route('portfolios.manage', $portfolio->management_token));

        $this->get(route('portfolios.show', $portfolio))
            ->assertOk()
            ->assertSee('creative-portfolio')
            ->assertSee('Field Notes')
            ->assertSee('Communication design')
            ->assertSee('Morgan Chen')
            ->assertDontSee($portfolio->management_token)
            ->assertDontSee('Edit details');

        $this->get(route('portfolios.manage', $portfolio->uuid))->assertNotFound();
        $this->get(route('portfolios.manage', $portfolio->management_token))->assertOk()->assertSee('Delete portfolio');
        $this->get(route('portfolios.edit', $portfolio->management_token))->assertOk()->assertSee('Morgan Chen');

        $this->put(route('portfolios.update', $portfolio->management_token), [
            'full_name' => 'Morgan Chen-Smith',
            'email' => 'morgan@example.com',
            'about_me' => 'Updated introduction.',
            'skills' => ['Research'],
        ])->assertRedirect(route('portfolios.manage', $portfolio->management_token));

        $this->assertDatabaseHas('portfolios', [
            'uuid' => $portfolio->uuid,
            'full_name' => 'Morgan Chen-Smith',
            'template' => 'creative',
        ]);

        $this->delete(route('portfolios.destroy', $portfolio->management_token))->assertRedirect(route('home'));
        $this->assertDatabaseMissing('portfolios', ['uuid' => $portfolio->uuid]);
    }

    public function test_only_the_three_supported_templates_can_be_selected(): void
    {
        $portfolio = Portfolio::create([
            'uuid' => '92d0d5aa-48a2-45f9-9cf0-d650bd593777',
            'management_token' => str_repeat('x', 64),
            'full_name' => 'Taylor Rivera',
            'email' => 'taylor@example.com',
        ]);

        $this->from(route('portfolios.templates', $portfolio->management_token))
            ->patch(route('portfolios.template', $portfolio->management_token), ['template' => 'unexpected'])
            ->assertSessionHasErrors('template');

        $this->assertSame('simple', $portfolio->fresh()->template);
    }

    public function test_profile_and_external_links_reject_non_http_schemes(): void
    {
        $this->from(route('portfolios.create'))
            ->post(route('portfolios.store'), [
                'full_name' => 'Casey Park',
                'email' => 'casey@example.com',
                'profile_image_url' => 'javascript://example.com/photo',
                'projects' => [['url' => 'javascript://example.com/project']],
                'links' => [['url' => 'javascript://example.com/profile']],
            ])
            ->assertSessionHasErrors([
                'profile_image_url',
                'projects.0.url',
                'links.0.url',
            ]);

        $this->assertDatabaseCount('portfolios', 0);
    }
}

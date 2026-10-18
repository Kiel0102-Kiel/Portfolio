<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.form', ['mode' => 'login']);
    }

    public function showRegister(): View
    {
        return view('auth.form', ['mode' => 'register']);
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge(['email' => mb_strtolower(trim($request->input('email', '')))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('account.index'));
    }

    public function login(Request $request): RedirectResponse
    {
        $request->merge(['email' => mb_strtolower(trim($request->input('email', '')))]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Those sign-in details do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('account.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function account(Request $request): View
    {
        return view('account.index', [
            'portfolios' => $request->user()->portfolios()->latest()->get(),
        ]);
    }
}

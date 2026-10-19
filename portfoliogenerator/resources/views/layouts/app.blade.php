<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Folioform') · Folioform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portfolio.css') }}">
    <script src="{{ asset('js/portfolio.js') }}" defer></script>
</head>
<body>
    <header class="site-header">
        <a class="wordmark" href="{{ route('home') }}"><span class="wordmark-mark">f.</span> folioform</a>
        <nav class="site-nav" aria-label="Main navigation">
            @auth
                <a class="nav-account {{ request()->routeIs('account.index') ? 'is-active' : '' }}" href="{{ route('account.index') }}" @if (request()->routeIs('account.index')) aria-current="page" @endif>My portfolios</a>
                <a class="nav-cta portfolio-cta {{ request()->routeIs('portfolios.create', 'portfolios.templates') ? 'is-active' : '' }}" href="{{ route('portfolios.create') }}" @if (request()->routeIs('portfolios.create', 'portfolios.templates')) aria-current="page" @endif>Create a portfolio <span aria-hidden="true">↗</span></a>
                <details class="settings-menu">
                    <summary class="{{ request()->routeIs('account.profile*') || request()->routeIs('account.settings') || request()->routeIs('account.password.*') ? 'is-active' : '' }}">Settings <span aria-hidden="true">⌄</span></summary>
                    <div class="settings-menu-items">
                        <a class="{{ request()->routeIs('account.profile*') ? 'is-active' : '' }}" href="{{ route('account.profile') }}" @if (request()->routeIs('account.profile*')) aria-current="page" @endif>Profile</a>
                        <a class="{{ request()->routeIs('account.settings') || request()->routeIs('account.password.*') ? 'is-active' : '' }}" href="{{ route('account.settings') }}" @if (request()->routeIs('account.settings') || request()->routeIs('account.password.*')) aria-current="page" @endif>Account settings</a>
                        <a href="{{ route('home') }}#how-it-works">How it works</a>
                        <form class="nav-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Sign out</button></form>
                    </div>
                </details>
            @endauth
        </nav>
    </header>
    @if (session('status'))
        <div class="flash-message" role="status">{{ session('status') }}</div>
    @endif
    <main>
        @yield('content')
    </main>
    <footer class="site-footer">
        <a class="wordmark" href="{{ route('home') }}"><span class="wordmark-mark">f.</span> folioform</a>
        <span>Make your work easy to find.</span>
    </footer>
</body>
</html>
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
            <a href="{{ route('home') }}#how-it-works">How it works</a>
            @auth
                <a class="nav-account" href="{{ route('account.index') }}">My portfolios</a>
                <form class="nav-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Sign out</button></form>
            @else
                <a class="nav-account" href="{{ route('login') }}">Sign in</a>
                <a class="nav-cta nav-register" href="{{ route('register') }}">Create account</a>
            @endauth
            <a class="nav-cta portfolio-cta" href="{{ route('portfolios.create') }}">Create a portfolio <span aria-hidden="true">↗</span></a>
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
@extends('layouts.app')

@section('title', $mode === 'register' ? 'Create account' : 'Sign in')

@section('content')
    <section class="auth-shell">
        <div class="auth-copy">
            <p class="eyebrow">FOLIOFORM ACCOUNT</p>
            <h1>{{ $mode === 'register' ? 'Your work, all together.' : 'Welcome!' }}</h1>
            <p class="muted-copy">{{ $mode === 'register' ? 'Create an account to keep your portfolios together.' : 'Sign in to find and manage your portfolios.' }}</p>
        </div>

        <form class="auth-form" method="POST" action="{{ $mode === 'register' ? route('register.store') : route('login.store') }}">
            @csrf
            @if ($errors->any())
                <div class="form-errors" role="alert"><strong>Please check your details.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            @if ($mode === 'register')
                <label class="field"><span>Name <b>*</b></span><input name="name" value="{{ old('name') }}" autocomplete="name" required autofocus></label>
            @endif
            <label class="field"><span>Email <b>*</b></span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required @if ($mode === 'login') autofocus @endif></label>
            <label class="field"><span>Password <b>*</b></span><input type="password" name="password" autocomplete="{{ $mode === 'register' ? 'new-password' : 'current-password' }}" required @if ($mode === 'register') minlength="8" @endif></label>
            @if ($mode === 'register')
                <label class="field"><span>Confirm password <b>*</b></span><input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label>
            @else
                <label class="remember-row"><input type="checkbox" name="remember" value="1"><span>Keep me signed in</span></label>
            @endif

            <button class="button button-dark" type="submit">{{ $mode === 'register' ? 'Create account' : 'Sign in' }} <span aria-hidden="true">↗</span></button>
            <p class="auth-switch">
                @if ($mode === 'register')
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                @else
                    New to Folioform? <a href="{{ route('register') }}">Create an account</a>
                @endif
            </p>
        </form>
    </section>
@endsection
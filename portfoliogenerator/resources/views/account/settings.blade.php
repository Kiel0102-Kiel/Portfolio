@extends('layouts.app')

@section('title', 'Account settings')

@section('content')
    <section class="page-shell account-shell">
        <a class="back-link" href="{{ route('account.index') }}">← <span>Back to portfolios</span></a>
        <nav class="account-subnav" aria-label="Account pages">
            <a href="{{ route('account.index') }}">My portfolios</a>
            <a href="{{ route('account.profile') }}">Profile</a>
            <a class="is-active" href="{{ route('account.settings') }}" aria-current="page">Account settings</a>
        </nav>
        <div class="page-heading account-page-heading">
            <p class="eyebrow">YOUR FOLIOFORM</p>
            <h1>Account settings.</h1>
            <p class="muted-copy">Keep your account secure by choosing a strong password.</p>
        </div>
        @if ($errors->any())
            <div class="form-errors" role="alert"><strong>There’s a small fix to make.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <form class="account-edit-form" method="POST" action="{{ route('account.password.update') }}">
            @csrf
            @method('PUT')
            <label class="field"><span>Current password</span><input type="password" name="current_password" autocomplete="current-password" required></label>
            <label class="field"><span>New password</span><input type="password" name="password" autocomplete="new-password" required></label>
            <label class="field"><span>Confirm new password</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <button class="button button-dark" type="submit">Update password</button>
        </form>
    </section>
@endsection

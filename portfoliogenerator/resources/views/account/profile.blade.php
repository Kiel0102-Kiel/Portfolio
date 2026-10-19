@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <section class="page-shell account-shell">
        <a class="back-link" href="{{ route('account.index') }}">← <span>Back to portfolios</span></a>
        <nav class="account-subnav" aria-label="Account pages">
            <a href="{{ route('account.index') }}">My portfolios</a>
            <a class="is-active" href="{{ route('account.profile') }}" aria-current="page">Profile</a>
            <a href="{{ route('account.settings') }}">Account settings</a>
        </nav>
        <div class="page-heading account-page-heading">
            <p class="eyebrow">YOUR FOLIOFORM</p>
            <h1>Your profile.</h1>
            <p class="muted-copy">Update the name and email address connected to your account.</p>
        </div>
        @if ($errors->any())
            <div class="form-errors" role="alert"><strong>There’s a small fix to make.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <form class="account-edit-form" method="POST" action="{{ route('account.profile.update') }}">
            @csrf
            @method('PUT')
            <label class="field"><span>Name</span><input name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" required></label>
            <label class="field"><span>Email</span><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" required></label>
            <button class="button button-dark" type="submit">Save profile</button>
        </form>
    </section>
@endsection

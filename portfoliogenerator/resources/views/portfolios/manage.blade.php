@extends('layouts.app')

@section('title', 'Manage your portfolio')

@section('content')
    <section class="manage-page page-shell">
        <a class="back-link" href="{{ route('portfolios.show', $portfolio) }}">← <span>View public portfolio</span></a>
        <div class="page-heading"><p class="eyebrow">YOUR SPACE / MANAGE</p><h1>Keep it<br><em>up to date.</em></h1><p class="muted-copy">Your public portfolio and private management link are listed below. Anyone with the private link can edit or delete this portfolio.</p></div>
        <div class="manage-card">
            <div class="manage-card-top"><div class="manage-avatar">{{ mb_substr($portfolio->full_name, 0, 1) }}</div><div><span class="eyebrow">PORTFOLIO OWNER</span><h2>{{ $portfolio->full_name }}</h2><p>{{ $portfolio->email }}</p></div><span class="template-pill">{{ ucfirst($portfolio->template) }} template</span></div>
            <div class="manage-link-row"><span>PUBLIC PORTFOLIO LINK</span><a href="{{ route('portfolios.show', $portfolio) }}">{{ route('portfolios.show', $portfolio) }}</a></div>
            <div class="manage-link-row private-link-row"><span>PRIVATE MANAGEMENT LINK</span><a href="{{ route('portfolios.manage', $portfolio->management_token) }}">{{ route('portfolios.manage', $portfolio->management_token) }}</a></div>
            <div class="manage-actions"><a class="button button-dark" href="{{ route('portfolios.show', $portfolio) }}">View portfolio <span aria-hidden="true">↗</span></a><a class="button button-outline" href="{{ route('portfolios.edit', $portfolio->management_token) }}">Edit information</a><a class="button button-outline" href="{{ route('portfolios.templates', $portfolio->management_token) }}">Change template</a></div>
            <div class="delete-zone"><div><strong>Remove this portfolio</strong><p>This permanently deletes the portfolio and its uploaded profile image.</p></div><form method="POST" action="{{ route('portfolios.destroy', $portfolio->management_token) }}" data-confirm="Delete this portfolio permanently? This cannot be undone.">@csrf @method('DELETE')<button class="button button-danger" type="submit">Delete portfolio</button></form></div>
        </div>
    </section>
@endsection
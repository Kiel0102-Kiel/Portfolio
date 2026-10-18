@extends('layouts.app')

@section('title', 'My portfolios')

@section('content')
    <section class="page-shell account-shell">
        <div class="account-heading">
            <div>
                <p class="eyebrow">YOUR FOLIOFORM</p>
                <h1>{{ auth()->user()->name }}’s portfolios</h1>
            </div>
            <a class="button button-dark" href="{{ route('portfolios.create') }}">Create a portfolio <span aria-hidden="true">↗</span></a>
        </div>

        @if ($portfolios->isEmpty())
            <div class="account-empty">
                <h2>Your next chapter starts here.</h2>
                <p class="muted-copy">Portfolios you create while signed in will be collected on this page.</p>
                <a class="text-link" href="{{ route('portfolios.create') }}">Start your first portfolio <span>↗</span></a>
            </div>
        @else
            <div class="account-list">
                @foreach ($portfolios as $portfolio)
                    <article class="account-item">
                        <div>
                            <h2>{{ $portfolio->full_name }}</h2>
                            <p>{{ ucfirst($portfolio->template) }} design <span aria-hidden="true">·</span> Updated {{ $portfolio->updated_at->format('M j, Y') }}</p>
                        </div>
                        <div class="account-actions">
                            <a class="button button-outline button-small" href="{{ route('portfolios.show', $portfolio) }}">View portfolio <span aria-hidden="true">↗</span></a>
                            <a class="button button-dark button-small" href="{{ route('portfolios.manage', $portfolio->management_token) }}">Manage</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
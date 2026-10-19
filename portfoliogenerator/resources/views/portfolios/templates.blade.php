@extends('layouts.app')

@section('title', 'Choose a template')

@section('content')
    <section class="template-page page-shell">
        <a class="back-link" href="{{ route('portfolios.manage', $portfolio->management_token) }}">← <span>Back to manage</span></a>
        <div class="page-heading"><p class="eyebrow">STEP 02 / THE FEEL</p><h1>Choose your<br><em>point of view.</em></h1><p class="muted-copy">Three distinct ways to tell the same story. You can switch designs later.</p></div>
        <div class="private-manage-note"><span>PRIVATE MANAGEMENT LINK</span><a href="{{ route('portfolios.manage', $portfolio->management_token) }}">Keep this link private so only you can edit your portfolio ↗</a></div>
        <form method="POST" action="{{ route('portfolios.template', $portfolio->management_token) }}">
            @csrf @method('PATCH')
            <div class="template-grid">
                <label class="template-choice">
                    <input type="radio" name="template" value="simple" @checked($portfolio->template === 'simple')>
                    <span class="template-card simple-card"><span class="template-card-meta">01 / QUIET CONFIDENCE</span><span class="mini-simple"><b>{{ $portfolio->full_name }}</b><i>{{ $portfolio->email }}</i><span></span><span></span><span></span></span><span class="template-card-copy"><strong>Simple</strong><small>Clean type, open space, all signal.</small></span></span>
                </label>
                <label class="template-choice">
                    <input type="radio" name="template" value="modern" @checked($portfolio->template === 'modern')>
                    <span class="template-card modern-card"><span class="template-card-meta">02 / STRUCTURED ENERGY</span><span class="mini-modern"><b>{{ $portfolio->full_name }}</b><span><i></i><i></i><i></i></span><small>SELECTED WORK</small></span><span class="template-card-copy"><strong>Modern</strong><small>Editorial grid, bold details, easy scanning.</small></span></span>
                </label>
                <label class="template-choice">
                    <input type="radio" name="template" value="creative" @checked($portfolio->template === 'creative')>
                    <span class="template-card creative-card"><span class="template-card-meta">03 / UNEXPECTED ANGLES</span><span class="mini-creative"><b>{{ Str::before($portfolio->full_name, ' ') }}</b><i>✳</i><span>MAKE<br>IT MATTER.</span></span><span class="template-card-copy"><strong>Creative</strong><small>Expressive color and a little more edge.</small></span></span>
                </label>
            </div>
            @error('template') <p class="input-error">{{ $message }}</p> @enderror
            <div class="template-submit"><p class="muted-copy">Selected: <strong id="selected-template-name">{{ ucfirst($portfolio->template) }}</strong></p><button class="button button-dark" type="submit">Preview portfolio <span aria-hidden="true">↗</span></button></div>
        </form>
    </section>
@endsection
@extends('layouts.app')

@section('title', 'Your work, well presented')

@section('content')
    <section class="home-hero">
        <div class="hero-copy">
            <p class="eyebrow"><span class="eyebrow-dot"></span> YOUR NEXT CHAPTER, ONLINE</p>
            <h1>A portfolio that<br><em>sounds like you.</em></h1>
            <p class="hero-description">Bring your experience, projects, and point of view together. Choose a design, make it yours, and share one clear link.</p>
            <a class="button button-dark" href="{{ route('portfolios.create') }}">Build your portfolio <span aria-hidden="true">↗</span></a>
            <p class="hero-note">Free to create <span>·</span> Sign in required</p>
        </div>
        <div class="hero-art" aria-label="Portfolio preview example">
            <div class="art-label">A LITTLE ABOUT ME <span>01 / 03</span></div>
            <div class="art-orbit orbit-one"></div>
            <div class="art-orbit orbit-two"></div>
            <div class="art-portrait"><span>J</span><i>✳</i></div>
            <div class="art-sticker">AVAILABLE<br>FOR WORK</div>
            <p class="art-caption">JORDAN<br><strong>LEE</strong><span>Designer &amp; storyteller</span></p>
            <div class="art-bottom"><span>SELECTED WORK</span><span>2022 — 2026</span></div>
        </div>
    </section>

    <section class="steps-section" id="how-it-works">
        <div class="section-heading">
            <p class="eyebrow">A SIMPLE START</p>
            <h2>From blank page<br>to <em>your own space.</em></h2>
        </div>
        <div class="steps-list">
            <article class="step-item"><span class="step-number">01</span><div><h3>Add your story</h3><p>Share the details that make your work yours. You can come back and edit any time.</p></div><span class="step-arrow">↘</span></article>
            <article class="step-item"><span class="step-number">02</span><div><h3>Pick a point of view</h3><p>Choose from three carefully composed designs, each with its own character.</p></div><span class="step-arrow">↘</span></article>
            <article class="step-item"><span class="step-number">03</span><div><h3>Make it yours</h3><p>Preview your page, refine your details, and share your personal portfolio link.</p></div><span class="step-arrow">↗</span></article>
        </div>
    </section>

    <section class="template-strip">
        <p class="eyebrow">THREE WAYS TO SHOW UP</p>
        <div class="template-strip-bottom"><h2>One story.<br><em>Three moods.</em></h2><a class="text-link" href="{{ route('portfolios.create') }}">Find your format <span>↗</span></a></div>
        <div class="mood-list"><span>01 / SIMPLE</span><span>02 / MODERN</span><span>03 / CREATIVE</span></div>
    </section>
@endsection
@extends('layouts.app')

@php
    $educationRows = old('education', $portfolio?->education ?? [['qualification' => '', 'institution' => '', 'year' => '', 'details' => '']]);
    $skillRows = old('skills', $portfolio?->skills ?? ['']);
    $projectRows = old('projects', $portfolio?->projects ?? [['name' => '', 'description' => '', 'url' => '']]);
    $experienceRows = old('experience', $portfolio?->experience ?? [['role' => '', 'company' => '', 'period' => '', 'details' => '']]);
    $linkRows = old('links', $portfolio?->links ?? [['label' => '', 'url' => '']]);
@endphp

@section('title', $portfolio ? 'Edit your details' : 'Create your portfolio')

@section('content')
    <section class="editor-shell">
        <div class="editor-intro">
            <a class="back-link" href="{{ $portfolio ? route('portfolios.manage', $portfolio->management_token) : route('home') }}">← <span>{{ $portfolio ? 'Back to manage' : 'Back home' }}</span></a>
            <p class="eyebrow">{{ $portfolio ? 'MAKE A CHANGE' : 'STEP 01 / YOUR STORY' }}</p>
            <h1>{{ $portfolio ? 'Refine your details.' : 'Let’s start with you.' }}</h1>
            <p class="muted-copy">Add what matters. Your information is saved privately to this portfolio link.</p>
            <div class="progress-track"><span style="width: {{ $portfolio ? '100%' : '33%' }}"></span></div>
            <div class="progress-label"><span>YOUR DETAILS</span><span>01 / 03</span></div>
        </div>

        <form class="portfolio-form" method="POST" action="{{ $portfolio ? route('portfolios.update', $portfolio->management_token) : route('portfolios.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($portfolio) @method('PUT') @endif
            @if ($errors->any())
                <div class="form-errors" role="alert"><strong>There’s a small fix to make.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">01</span><div><h2>The essentials</h2><p>How people will recognize and reach you.</p></div></div>
                <div class="form-grid">
                    <label class="field field-wide"><span>Full name <b>*</b></span><input name="full_name" value="{{ old('full_name', $portfolio?->full_name) }}" autocomplete="name" required placeholder="e.g. Jordan Lee"></label>
                    <label class="field"><span>Email <b>*</b></span><input type="email" name="email" value="{{ old('email', $portfolio?->email) }}" autocomplete="email" required placeholder="you@example.com"></label>
                    <label class="field"><span>Contact number</span><input name="contact_number" value="{{ old('contact_number', $portfolio?->contact_number) }}" autocomplete="tel" placeholder="+1 555 0100"></label>
                    <label class="field field-wide"><span>Address or location</span><input name="address" value="{{ old('address', $portfolio?->address) }}" autocomplete="street-address" placeholder="City, Country"></label>
                    <label class="field field-wide"><span>Profile picture</span><input type="file" name="profile_image" accept="image/*"><small>JPG, PNG, or WebP up to 4 MB. Or use an image URL below.</small></label>
                    <label class="field field-wide"><span>Profile picture URL</span><input type="url" name="profile_image_url" value="{{ old('profile_image_url', $portfolio?->isRemoteProfileImage() ? $portfolio->profile_image : '') }}" placeholder="https://example.com/your-photo.jpg"><small>Use a public image URL for reliable hosting without extra file storage.</small></label>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">02</span><div><h2>Your introduction</h2><p>A few lines about what you do and what drives you.</p></div></div>
                <label class="field"><span>About me</span><textarea name="about_me" rows="5" placeholder="I’m a... I care about... I’m looking for...">{{ old('about_me', $portfolio?->about_me) }}</textarea></label>
            </section>

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">03</span><div><h2>Education</h2><p>Learning, qualifications, and the places that shaped you.</p></div></div>
                <div class="repeater" data-repeater>
                    <div class="repeat-list" data-repeat-list>
                        @foreach ($educationRows as $index => $row)
                            <div class="repeat-card" data-repeat-row>
                                <button class="remove-row" type="button" data-remove-row aria-label="Remove education entry">×</button>
                                <div class="form-grid">
                                    <label class="field"><span>Qualification</span><input name="education[{{ $index }}][qualification]" value="{{ $row['qualification'] ?? '' }}" placeholder="BSc Computer Science"></label>
                                    <label class="field"><span>Institution</span><input name="education[{{ $index }}][institution]" value="{{ $row['institution'] ?? '' }}" placeholder="University name"></label>
                                    <label class="field"><span>Year</span><input name="education[{{ $index }}][year]" value="{{ $row['year'] ?? '' }}" placeholder="2022 – 2026"></label>
                                    <label class="field"><span>Notes</span><input name="education[{{ $index }}][details]" value="{{ $row['details'] ?? '' }}" placeholder="Honors, focus, or relevant detail"></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <template data-repeat-template><div class="repeat-card" data-repeat-row><button class="remove-row" type="button" data-remove-row aria-label="Remove education entry">×</button><div class="form-grid"><label class="field"><span>Qualification</span><input name="education[__INDEX__][qualification]" placeholder="BSc Computer Science"></label><label class="field"><span>Institution</span><input name="education[__INDEX__][institution]" placeholder="University name"></label><label class="field"><span>Year</span><input name="education[__INDEX__][year]" placeholder="2022 – 2026"></label><label class="field"><span>Notes</span><input name="education[__INDEX__][details]" placeholder="Honors, focus, or relevant detail"></label></div></div></template>
                    <button class="add-row" type="button" data-add-row>＋ Add education</button>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">04</span><div><h2>Skills</h2><p>Tools, strengths, and things you do especially well.</p></div></div>
                <div class="repeater" data-repeater>
                    <div class="repeat-list skill-repeat-list" data-repeat-list>
                        @foreach ($skillRows as $index => $skill)
                            <div class="skill-row" data-repeat-row><input name="skills[{{ $index }}]" value="{{ $skill }}" placeholder="e.g. Visual design"><button class="remove-row" type="button" data-remove-row aria-label="Remove skill">×</button></div>
                        @endforeach
                    </div>
                    <template data-repeat-template><div class="skill-row" data-repeat-row><input name="skills[__INDEX__]" placeholder="e.g. Visual design"><button class="remove-row" type="button" data-remove-row aria-label="Remove skill">×</button></div></template>
                    <button class="add-row" type="button" data-add-row>＋ Add a skill</button>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">05</span><div><h2>Selected projects</h2><p>Show a few examples of what you’ve made.</p></div></div>
                <div class="repeater" data-repeater>
                    <div class="repeat-list" data-repeat-list>
                        @foreach ($projectRows as $index => $row)
                            <div class="repeat-card" data-repeat-row><button class="remove-row" type="button" data-remove-row aria-label="Remove project">×</button><div class="form-grid"><label class="field"><span>Project name</span><input name="projects[{{ $index }}][name]" value="{{ $row['name'] ?? '' }}" placeholder="Project title"></label><label class="field"><span>Project link</span><input type="url" name="projects[{{ $index }}][url]" value="{{ $row['url'] ?? '' }}" placeholder="https://"></label><label class="field field-wide"><span>What did you do?</span><textarea name="projects[{{ $index }}][description]" rows="3" placeholder="The challenge, your contribution, and the result.">{{ $row['description'] ?? '' }}</textarea></label></div></div>
                        @endforeach
                    </div>
                    <template data-repeat-template><div class="repeat-card" data-repeat-row><button class="remove-row" type="button" data-remove-row aria-label="Remove project">×</button><div class="form-grid"><label class="field"><span>Project name</span><input name="projects[__INDEX__][name]" placeholder="Project title"></label><label class="field"><span>Project link</span><input type="url" name="projects[__INDEX__][url]" placeholder="https://"></label><label class="field field-wide"><span>What did you do?</span><textarea name="projects[__INDEX__][description]" rows="3" placeholder="The challenge, your contribution, and the result."></textarea></label></div></div></template>
                    <button class="add-row" type="button" data-add-row>＋ Add a project</button>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">06</span><div><h2>Experience</h2><p>Work, volunteering, internships, or other relevant roles.</p></div></div>
                <div class="repeater" data-repeater>
                    <div class="repeat-list" data-repeat-list>
                        @foreach ($experienceRows as $index => $row)
                            <div class="repeat-card" data-repeat-row><button class="remove-row" type="button" data-remove-row aria-label="Remove experience">×</button><div class="form-grid"><label class="field"><span>Role</span><input name="experience[{{ $index }}][role]" value="{{ $row['role'] ?? '' }}" placeholder="Product design intern"></label><label class="field"><span>Company</span><input name="experience[{{ $index }}][company]" value="{{ $row['company'] ?? '' }}" placeholder="Company name"></label><label class="field"><span>Period</span><input name="experience[{{ $index }}][period]" value="{{ $row['period'] ?? '' }}" placeholder="Jun 2024 – Present"></label><label class="field"><span>What you did</span><input name="experience[{{ $index }}][details]" value="{{ $row['details'] ?? '' }}" placeholder="A short description of your role"></label></div></div>
                        @endforeach
                    </div>
                    <template data-repeat-template><div class="repeat-card" data-repeat-row><button class="remove-row" type="button" data-remove-row aria-label="Remove experience">×</button><div class="form-grid"><label class="field"><span>Role</span><input name="experience[__INDEX__][role]" placeholder="Product design intern"></label><label class="field"><span>Company</span><input name="experience[__INDEX__][company]" placeholder="Company name"></label><label class="field"><span>Period</span><input name="experience[__INDEX__][period]" placeholder="Jun 2024 – Present"></label><label class="field"><span>What you did</span><input name="experience[__INDEX__][details]" placeholder="A short description of your role"></label></div></div></template>
                    <button class="add-row" type="button" data-add-row>＋ Add experience</button>
                </div>
            </section>

            <section class="form-section">
                <div class="form-section-heading"><span class="section-index">07</span><div><h2>Elsewhere online</h2><p>Give people another way to find your work.</p></div></div>
                <div class="repeater" data-repeater>
                    <div class="repeat-list" data-repeat-list>
                        @foreach ($linkRows as $index => $row)
                            <div class="form-grid link-row" data-repeat-row><label class="field"><span>Link label</span><input name="links[{{ $index }}][label]" value="{{ $row['label'] ?? '' }}" placeholder="LinkedIn"></label><label class="field"><span>URL</span><input type="url" name="links[{{ $index }}][url]" value="{{ $row['url'] ?? '' }}" placeholder="https://"></label><button class="remove-row" type="button" data-remove-row aria-label="Remove link">×</button></div>
                        @endforeach
                    </div>
                    <template data-repeat-template><div class="form-grid link-row" data-repeat-row><label class="field"><span>Link label</span><input name="links[__INDEX__][label]" placeholder="LinkedIn"></label><label class="field"><span>URL</span><input type="url" name="links[__INDEX__][url]" placeholder="https://"></label><button class="remove-row" type="button" data-remove-row aria-label="Remove link">×</button></div></template>
                    <button class="add-row" type="button" data-add-row>＋ Add a link</button>
                </div>
            </section>

            <div class="form-submit-row"><p><span class="required-mark">*</span> Required fields</p><button class="button button-dark" type="submit">{{ $portfolio ? 'Save changes' : 'Save and choose a design' }} <span aria-hidden="true">↗</span></button></div>
        </form>
    </section>
@endsection
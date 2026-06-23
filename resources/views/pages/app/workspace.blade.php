@extends('layouts.app')

@section('title', 'Dashboard | Educonnect')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/workspace.css') }}">
@endpush

@section('navigation')
    <x-navigation.menu active="dashboard" />
@endsection

@section('sidebar-footer')
    <x-navigation.user :user="$user" />
@endsection

@section('page-context')
    <span class="page-context">Dashboard</span>
@endsection

@section('topbar-actions')
    <button class="button button--secondary" type="button" data-logout-button data-login-url="{{ route('login') }}">
        Sign out
    </button>
@endsection

@section('content')
    <div class="dashboard" data-dashboard data-student-id="{{ $student->id }}">
        <header class="dashboard-heading">
            <p class="dashboard-heading__eyebrow">Academic workspace</p>
            <h1>Welcome back, {{ strtok($user->name, ' ') }}</h1>
            <p>Your current profile, activity, and recommendations.</p>
        </header>

        <div class="dashboard-loading" data-dashboard-loading role="status" aria-live="polite">
            <span class="sr-only">Loading dashboard</span>
            <div class="skeleton skeleton--profile"></div>
            <div class="skeleton-grid" aria-hidden="true">
                <span class="skeleton skeleton--metric"></span>
                <span class="skeleton skeleton--metric"></span>
                <span class="skeleton skeleton--metric"></span>
                <span class="skeleton skeleton--metric"></span>
            </div>
        </div>

        <section class="dashboard-error" data-dashboard-error hidden aria-labelledby="dashboard-error-heading">
            <p class="dashboard-error__eyebrow">Connection issue</p>
            <h2 id="dashboard-error-heading">Dashboard unavailable</h2>
            <p data-dashboard-error-message>We could not load your workspace.</p>
            <button class="button button--secondary" type="button" data-dashboard-retry>Try again</button>
        </section>

        <div data-dashboard-content hidden>
            <section class="profile-band" aria-labelledby="profile-heading">
                <div class="profile-band__summary">
                    <div>
                        <p class="section-eyebrow">Academic profile</p>
                        <h2 id="profile-heading" data-profile-name>Student profile</h2>
                    </div>
                    <div class="profile-progress">
                        <div class="profile-progress__label">
                            <span>Profile completion</span>
                            <strong data-profile-completion>0%</strong>
                        </div>
                        <progress data-profile-progress max="100" value="0">0%</progress>
                    </div>
                </div>

                <dl class="profile-facts">
                    <div>
                        <dt>University</dt>
                        <dd data-profile-university>Not set</dd>
                    </div>
                    <div>
                        <dt>Academic field</dt>
                        <dd data-profile-field>Not set</dd>
                    </div>
                    <div>
                        <dt>Semester</dt>
                        <dd data-profile-semester>Not set</dd>
                    </div>
                    <div>
                        <dt>Current level</dt>
                        <dd data-profile-level>Not set</dd>
                    </div>
                </dl>
            </section>

            <section class="metric-grid" aria-label="Workspace totals">
                <article class="metric-card metric-card--green">
                    <p>Saved resources</p>
                    <strong data-count-saved-resources>0</strong>
                </article>
                <article class="metric-card metric-card--blue">
                    <p>Saved templates</p>
                    <strong data-count-saved-templates>0</strong>
                </article>
                <article class="metric-card metric-card--orange">
                    <p>Documents</p>
                    <strong data-count-documents>0</strong>
                </article>
                <article class="metric-card metric-card--neutral">
                    <p>Research topics</p>
                    <strong data-count-research-topics>0</strong>
                </article>
            </section>

            <div class="dashboard-columns">
                <section class="resource-section" aria-labelledby="recommendations-heading">
                    <header class="resource-section__heading">
                        <div>
                            <p class="section-eyebrow">Matched to your profile</p>
                            <h2 id="recommendations-heading">Recommended for you</h2>
                        </div>
                    </header>
                    <div class="resource-list" data-recommendation-list></div>
                </section>

                <section class="resource-section" aria-labelledby="recent-heading">
                    <header class="resource-section__heading">
                        <div>
                            <p class="section-eyebrow">Recently added</p>
                            <h2 id="recent-heading">Explore resources</h2>
                        </div>
                    </header>
                    <div class="resource-list" data-recent-list></div>
                </section>
            </div>
        </div>

        <noscript>
            <div class="alert alert--warning">JavaScript is required to load dashboard data.</div>
        </noscript>

        <div class="workspace-feedback" data-auth-feedback hidden></div>
    </div>
@endsection

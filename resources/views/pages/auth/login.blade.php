@extends('layouts.guest')

@section('title', 'Sign in | EduConnect')

@section('content')
    <div class="auth-shell">
        <section class="auth-visual" aria-label="EduConnect academic workspace">
            <img
                class="auth-visual__image"
                src="{{ asset('assets/images/auth-workspace.png') }}"
                alt="An organized academic desk with a laptop, research papers, and an open notebook"
            >
            <div class="auth-visual__content">
                <x-brand.lockup class="brand-lockup--inverse" />
                <div>
                    <p class="auth-visual__eyebrow">Academic work, brought together</p>
                    <h1 class="auth-visual__title">Make space for the work that matters.</h1>
                </div>
            </div>
        </section>

        <section class="auth-panel" aria-labelledby="login-heading">
            <div class="auth-panel__mobile-brand">
                <x-brand.lockup />
            </div>

            <div class="auth-form-wrap">
                <header class="auth-heading">
                    <p class="auth-heading__eyebrow">Welcome back</p>
                    <h2 id="login-heading">Sign in to EduConnect</h2>
                    <p>Return to your academic workspace.</p>
                </header>

                <div class="auth-feedback" data-auth-feedback hidden></div>

                <form class="auth-form" method="post" action="{{ url('/api/v1/auth/login') }}" data-login-form>
                    <x-ui.input
                        name="email"
                        label="Email address"
                        type="email"
                        autocomplete="email"
                        inputmode="email"
                        placeholder="you@university.edu"
                        required
                        autofocus
                    />

                    <x-ui.input
                        name="password"
                        label="Password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        required
                    />

                    <label class="checkbox">
                        <input class="checkbox__control" type="checkbox" name="remember" value="1">
                        <span>Keep me signed in on this device</span>
                    </label>

                    <x-ui.button type="submit" block data-submit-button>
                        Sign in
                    </x-ui.button>
                </form>
            </div>

            <p class="auth-panel__footer">EduConnect Database Lab MVP</p>
        </section>
    </div>
@endsection

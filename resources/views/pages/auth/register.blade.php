@extends('layouts.guest')

@section('title', 'Create account | Educonnect')

@section('content')
    <div class="auth-shell">
        <section class="auth-visual" aria-label="Educonnect academic workspace">
            <img
                class="auth-visual__image"
                src="{{ asset('assets/images/auth-workspace.png') }}"
                alt="An organized academic desk with a laptop, research papers, and an open notebook"
            >
            <div class="auth-visual__content">
                <x-brand.lockup class="brand-lockup--inverse" />
                <div>
                    <p class="auth-visual__eyebrow">Built for focused study</p>
                    <h1 class="auth-visual__title">Start with a clear workspace.</h1>
                </div>
            </div>
        </section>

        <section class="auth-panel" aria-labelledby="register-heading">
            <div class="auth-panel__mobile-brand">
                <x-brand.lockup />
            </div>

            <div class="auth-form-wrap">
                <header class="auth-heading auth-heading--compact">
                    <p class="auth-heading__eyebrow">Get started</p>
                    <h2 id="register-heading">Create your account</h2>
                    <p>Use your university email to begin.</p>
                </header>

                <div class="auth-feedback" data-auth-feedback hidden></div>

                <form
                    class="auth-form"
                    method="post"
                    action="{{ url('/api/v1/auth/register') }}"
                    data-auth-form="register"
                    data-success-url="{{ route('workspace') }}"
                >
                    <input type="hidden" name="role" value="STUDENT">

                    <x-ui.input
                        name="name"
                        label="Full name"
                        autocomplete="name"
                        placeholder="Your full name"
                        required
                        autofocus
                    />

                    <x-ui.input
                        name="email"
                        label="Email address"
                        type="email"
                        autocomplete="email"
                        inputmode="email"
                        placeholder="you@university.edu"
                        required
                    />

                    <x-ui.input
                        name="password"
                        label="Password"
                        type="password"
                        autocomplete="new-password"
                        minlength="6"
                        hint="Use at least 6 characters."
                        placeholder="Create a password"
                        required
                    />

                    <x-ui.input
                        name="password_confirmation"
                        label="Confirm password"
                        type="password"
                        autocomplete="new-password"
                        minlength="6"
                        placeholder="Repeat your password"
                        required
                    />

                    <x-ui.button type="submit" block data-submit-button>
                        Create account
                    </x-ui.button>
                </form>

                <p class="auth-switch">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </p>
            </div>

        </section>
    </div>
@endsection

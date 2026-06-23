@extends('layouts.app')

@section('title', 'Profile setup | Educonnect')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/onboarding.css') }}">
@endpush

@section('navigation')
    <x-navigation.menu />
@endsection

@section('sidebar-footer')
    <x-navigation.user :user="$user" />
@endsection

@section('page-context')
    <span class="page-context">Profile setup</span>
@endsection

@section('topbar-actions')
    <button class="button button--secondary" type="button" data-logout-button data-login-url="{{ route('login') }}">
        Sign out
    </button>
@endsection

@section('content')
    <div class="onboarding" data-onboarding data-success-url="{{ route('workspace') }}">
        <header class="onboarding__heading">
            <p class="onboarding__eyebrow">Academic profile</p>
            <h1>Tell us what you are studying</h1>
            <p>This information shapes your dashboard and recommendations.</p>
        </header>

        <div class="onboarding__feedback" data-onboarding-feedback hidden></div>
        <button class="button button--secondary onboarding__retry" type="button" data-reference-retry hidden>
            Retry loading options
        </button>

        <form
            class="onboarding-form"
            method="post"
            action="{{ url('/api/v1/students/onboarding') }}"
            data-onboarding-form
        >
            <div class="onboarding-form__grid">
                <x-ui.select name="university_id" label="University" data-university-select disabled required>
                    <option value="">Loading universities...</option>
                </x-ui.select>

                <x-ui.select name="academic_field_id" label="Academic field" data-field-select disabled required>
                    <option value="">Loading academic fields...</option>
                </x-ui.select>

                <x-ui.input
                    name="department"
                    label="Department"
                    autocomplete="organization-title"
                    placeholder="e.g. Computer Science and Engineering"
                />

                <x-ui.input
                    name="semester"
                    label="Current semester"
                    inputmode="numeric"
                    placeholder="e.g. 6"
                />

                <x-ui.select name="skill_level" label="Current level" required>
                    <option value="">Select your level</option>
                    <option value="BEGINNER">Beginner</option>
                    <option value="INTERMEDIATE">Intermediate</option>
                    <option value="ADVANCED">Advanced</option>
                </x-ui.select>

                <x-ui.select name="goal_type" label="Primary academic goal" hint="You can refine your preferences later.">
                    <option value="">Select a goal</option>
                    <option value="ASSIGNMENT">Assignment work</option>
                    <option value="RESEARCH">Research</option>
                    <option value="LAB">Laboratory work</option>
                    <option value="CODING">Coding practice</option>
                    <option value="EXAM">Exam preparation</option>
                    <option value="PROJECT">Project development</option>
                </x-ui.select>
            </div>

            <div class="onboarding-form__actions">
                <x-ui.button type="submit" data-submit-button disabled>
                    Continue to dashboard
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection

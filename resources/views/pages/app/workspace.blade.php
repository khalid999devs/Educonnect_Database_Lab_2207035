@extends('layouts.app')

@section('title', 'Workspace | Educonnect')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/workspace.css') }}">
@endpush

@section('navigation')
    <x-navigation.link :href="route('workspace')" active>Overview</x-navigation.link>
@endsection

@section('sidebar-footer')
    <div class="sidebar-user">
        <span class="sidebar-user__avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        <span class="sidebar-user__details">
            <strong>{{ $user->name }}</strong>
            <span>{{ ucfirst(strtolower($user->role)) }}</span>
        </span>
    </div>
@endsection

@section('page-context')
    <span class="page-context">Workspace</span>
@endsection

@section('topbar-actions')
    <button class="button button--secondary" type="button" data-logout-button data-login-url="{{ route('login') }}">
        Sign out
    </button>
@endsection

@section('content')
    <section class="workspace-heading" aria-labelledby="workspace-heading">
        <p class="workspace-heading__eyebrow">Account ready</p>
        <h1 id="workspace-heading">Welcome, {{ strtok($user->name, ' ') }}</h1>
        <p>Everything is ready for your next study session.</p>
    </section>

    <section class="account-band" aria-labelledby="account-heading">
        <div class="account-band__heading">
            <div>
                <p class="account-band__eyebrow">Signed-in account</p>
                <h2 id="account-heading">Account details</h2>
            </div>
            <span class="status-badge status-badge--success">Active</span>
        </div>

        <dl class="account-details">
            <div>
                <dt>Name</dt>
                <dd>{{ $user->name }}</dd>
            </div>
            <div>
                <dt>Email</dt>
                <dd>{{ $user->email }}</dd>
            </div>
            <div>
                <dt>Role</dt>
                <dd>{{ ucfirst(strtolower($user->role)) }}</dd>
            </div>
        </dl>
    </section>

    <div class="workspace-feedback" data-auth-feedback hidden></div>
@endsection

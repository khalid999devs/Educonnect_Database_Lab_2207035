<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ url('/api/v1') }}">

    <title>@yield('title', config('app.name'))</title>

    <link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layouts.css') }}">
    @stack('styles')
</head>
<body class="app-page">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div class="app-shell" data-app-shell>
        <header class="mobile-header">
            <x-brand.lockup compact />
            <button class="button button--quiet" type="button" data-sidebar-toggle aria-controls="app-sidebar" aria-expanded="false">
                Menu
            </button>
        </header>

        <aside class="app-sidebar" id="app-sidebar" data-sidebar>
            <div class="app-sidebar__brand">
                <x-brand.lockup />
            </div>

            <nav class="app-navigation" aria-label="Primary navigation">
                @yield('navigation')
            </nav>

            <div class="app-sidebar__footer">
                @yield('sidebar-footer')
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <div class="app-topbar__context">@yield('page-context')</div>
                <div class="app-topbar__actions">@yield('topbar-actions')</div>
            </header>

            <main class="app-content" id="main-content">
                @yield('content')
            </main>
        </div>

        <button class="sidebar-backdrop" type="button" data-sidebar-backdrop aria-label="Close navigation"></button>
    </div>

    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>

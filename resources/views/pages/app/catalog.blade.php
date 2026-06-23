@extends('layouts.app')

@section('title', $catalog['label'].' | Educonnect')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/catalog.css') }}">
@endpush

@section('navigation')
    <x-navigation.menu :active="$catalog['key']" />
@endsection

@section('sidebar-footer')
    <x-navigation.user :user="$user" />
@endsection

@section('page-context')
    <span class="page-context">{{ $catalog['label'] }}</span>
@endsection

@section('topbar-actions')
    <button class="button button--secondary" type="button" data-logout-button data-login-url="{{ route('login') }}">
        Sign out
    </button>
@endsection

@section('content')
    <div
        class="catalog"
        data-catalog
        data-catalog-type="{{ $catalog['key'] }}"
        data-student-id="{{ $student->id }}"
    >
        <header class="catalog-heading">
            <p class="catalog-heading__eyebrow">{{ $catalog['eyebrow'] }}</p>
            <h1>{{ $catalog['label'] }}</h1>
            <p>{{ $catalog['description'] }}</p>
        </header>

        <form class="catalog-filters" data-catalog-filters role="search">
            <x-ui.input
                name="search"
                label="Search"
                type="search"
                autocomplete="off"
                :placeholder="'Search '.strtolower($catalog['label'])"
            />

            <div class="field">
                <label class="field__label" for="catalog-category">Category</label>
                <select class="field__control field__control--select" id="catalog-category" data-category-filter disabled>
                    <option value="">Loading categories...</option>
                </select>
            </div>

            <div class="field">
                <label class="field__label" for="catalog-field">Academic field</label>
                <select class="field__control field__control--select" id="catalog-field" name="academic_field_id" data-field-filter disabled>
                    <option value="">Loading fields...</option>
                </select>
            </div>

            <div class="field">
                <label class="field__label" for="catalog-extra" data-extra-filter-label>Type</label>
                <select class="field__control field__control--select" id="catalog-extra" data-extra-filter></select>
            </div>

            <div class="catalog-filters__actions">
                <x-ui.button type="submit">Apply filters</x-ui.button>
                <x-ui.button type="button" variant="quiet" data-filter-clear>Clear</x-ui.button>
            </div>
        </form>

        <div class="catalog-feedback" data-catalog-feedback hidden></div>

        <div class="catalog-loading" data-catalog-loading role="status" aria-live="polite">
            <span class="sr-only">Loading {{ strtolower($catalog['label']) }}</span>
            <div class="catalog-skeleton-grid" aria-hidden="true">
                @for ($i = 0; $i < 6; $i++)
                    <span class="catalog-skeleton"></span>
                @endfor
            </div>
        </div>

        <section class="catalog-error" data-catalog-error hidden aria-labelledby="catalog-error-heading">
            <h2 id="catalog-error-heading">Unable to load {{ strtolower($catalog['label']) }}</h2>
            <p data-catalog-error-message>Please try again.</p>
            <button class="button button--secondary" type="button" data-catalog-retry>Try again</button>
        </section>

        <div data-catalog-content hidden>
            <div class="catalog-results-bar">
                <p data-results-summary>0 results</p>
                <p data-page-summary>Page 1</p>
            </div>

            <section class="catalog-grid" data-catalog-grid aria-label="{{ $catalog['label'] }} results"></section>

            <nav class="catalog-pagination" aria-label="Catalog pagination">
                <button class="button button--secondary" type="button" data-page-previous>Previous</button>
                <span data-pagination-label>Page 1 of 1</span>
                <button class="button button--secondary" type="button" data-page-next>Next</button>
            </nav>
        </div>

        <dialog class="catalog-dialog" data-catalog-dialog aria-labelledby="catalog-dialog-title">
            <div class="catalog-dialog__panel">
                <header class="catalog-dialog__header">
                    <div>
                        <p data-detail-category>Category</p>
                        <h2 id="catalog-dialog-title" data-detail-title>Item details</h2>
                    </div>
                    <form method="dialog">
                        <button class="button button--quiet" type="submit">Close</button>
                    </form>
                </header>

                <div class="catalog-dialog__loading" data-detail-loading hidden>Loading details...</div>
                <div class="catalog-dialog__content" data-detail-content>
                    <p class="catalog-dialog__description" data-detail-description></p>
                    <dl class="catalog-detail-facts" data-detail-facts></dl>
                </div>

                <footer class="catalog-dialog__actions">
                    <div class="catalog-dialog__feedback" data-detail-feedback hidden></div>
                    <div class="catalog-dialog__commands">
                        <a class="button button--secondary" href="#" target="_blank" rel="noopener noreferrer" data-detail-link>
                            Open original
                        </a>
                        <button class="button button--primary" type="button" data-catalog-action hidden></button>
                    </div>
                </footer>
            </div>
        </dialog>

        <div class="workspace-feedback" data-auth-feedback hidden></div>
    </div>
@endsection

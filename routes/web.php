<?php

use App\Http\Controllers\Web\CatalogController;
use App\Http\Controllers\Web\OnboardingController;
use App\Http\Controllers\Web\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'pages.auth.login')->name('login');
    Route::view('/register', 'pages.auth.register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/app', WorkspaceController::class)->name('workspace');
    Route::get('/app/onboarding', OnboardingController::class)->name('onboarding');
    Route::get('/app/{catalog}', CatalogController::class)
        ->whereIn('catalog', ['resources', 'tools', 'templates'])
        ->name('catalog.index');
});

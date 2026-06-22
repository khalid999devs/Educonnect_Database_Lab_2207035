<?php

use App\Http\Controllers\Web\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'pages.auth.login')->name('login');
    Route::view('/register', 'pages.auth.register')->name('register');
});

Route::get('/app', WorkspaceController::class)
    ->middleware('auth')
    ->name('workspace');

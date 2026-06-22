<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');
Route::view('/login', 'pages.auth.login')->name('login');

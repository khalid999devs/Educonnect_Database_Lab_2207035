<?php

use App\Http\Controllers\Api\AcademicDocumentController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DatabaseHealthController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\ResearchTopicController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SavedResourceController;
use App\Http\Controllers\Api\SavedTemplateController;
use App\Http\Controllers\Api\StudentOnboardingController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\ToolController;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health/database', DatabaseHealthController::class)->name('health.database');

    Route::prefix('auth')
        ->name('auth.')
        ->middleware(StartSession::class)
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('/register', 'register')->name('register');
            Route::post('/login', 'login')->name('login');

            Route::middleware('auth')->group(function () {
                Route::get('/me', 'me')->name('me');
                Route::post('/logout', 'logout')->name('logout');
            });
        });

    Route::prefix('students')->name('students.')->controller(StudentOnboardingController::class)->group(function () {
        Route::post('/onboarding', 'store')->name('onboarding');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/{id}', 'update')->name('update');
    });

    Route::prefix('documents')->name('documents.')->controller(AcademicDocumentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/{id}/extracted-data', 'addExtractedData')->name('extracted-data.store');
    });

    Route::prefix('resources')->name('resources.')->group(function () {
        Route::controller(ResourceController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::post('/{id}/save', [SavedResourceController::class, 'store'])->name('save');
        Route::post('/{id}/approve', [AdminController::class, 'approveResource'])->name('approve');
    });

    Route::prefix('tools')->name('tools.')->controller(ToolController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::prefix('templates')->name('templates.')->group(function () {
        Route::controller(TemplateController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::post('/{id}/save', [SavedTemplateController::class, 'store'])->name('save');
        Route::post('/{id}/purchase', [SavedTemplateController::class, 'purchase'])->name('purchase');
        Route::post('/{id}/approve', [AdminController::class, 'approveTemplate'])->name('approve');
    });

    Route::prefix('research-topics')->name('research-topics.')->controller(ResearchTopicController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}/collections', 'collections')->name('collections.index');
        Route::post('/{id}/collections', 'storeCollection')->name('collections.store');
    });

    Route::prefix('reviews')->name('reviews.')->controller(ReviewController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::get('/dashboard/student/{id}', [DashboardController::class, 'show'])->name('dashboard.student');
    Route::get('/recommendations/student/{id}', [RecommendationController::class, 'index'])->name('recommendations.student');
});

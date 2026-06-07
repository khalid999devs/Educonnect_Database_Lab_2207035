<?php

use App\Http\Controllers\Api\DatabaseHealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health/database', DatabaseHealthController::class);
});

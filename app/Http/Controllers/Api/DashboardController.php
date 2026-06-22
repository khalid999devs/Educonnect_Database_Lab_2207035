<?php

namespace App\Http\Controllers\Api;

use App\Services\DashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends ApiController
{
    public function show(int $studentId, DashboardService $dashboard): JsonResponse
    {
        return ApiResponse::success(
            'Student dashboard retrieved',
            $dashboard->getForStudent($studentId),
        );
    }
}

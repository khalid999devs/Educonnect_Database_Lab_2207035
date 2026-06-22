<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Students\RecommendationRequest;
use App\Services\RecommendationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class RecommendationController extends ApiController
{
    public function index(
        RecommendationRequest $request,
        int $studentId,
        RecommendationService $recommendations,
    ): JsonResponse {
        return ApiResponse::success(
            'Student recommendations retrieved',
            $recommendations->getForStudent(
                $studentId,
                (int) $request->validated('limit', 10),
            ),
        );
    }
}

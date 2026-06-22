<?php

namespace App\Http\Controllers\Api;

use App\Services\ReferenceDataService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReferenceDataController extends ApiController
{
    public function __invoke(ReferenceDataService $referenceData): JsonResponse
    {
        return ApiResponse::success(
            'Reference data retrieved',
            $referenceData->getAll(),
        );
    }
}

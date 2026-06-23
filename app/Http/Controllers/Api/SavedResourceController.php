<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Resources\StudentResourceActionRequest;
use App\Models\SavedResource;
use App\Services\OracleProcedureService;
use App\Services\StudentContextService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SavedResourceController extends ApiController
{
    public function store(
        StudentResourceActionRequest $request,
        int $id,
        OracleProcedureService $oracleProcedures,
        StudentContextService $studentContext,
    ): JsonResponse {
        $student = $studentContext->forUser($request->user());

        if (! $student) {
            return ApiResponse::error('Student profile is required', null, 422);
        }

        $studentId = (int) $student->id;

        return $this->runOracleOperation(function () use ($studentId, $id, $oracleProcedures): SavedResource {
            $oracleProcedures->saveResource($studentId, $id);

            return SavedResource::query()
                ->with('resource')
                ->where('student_id', $studentId)
                ->where('resource_id', $id)
                ->firstOrFail();
        }, 'Resource saved successfully', 201);
    }
}

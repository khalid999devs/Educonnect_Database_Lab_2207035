<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Resources\StudentResourceActionRequest;
use App\Models\SavedResource;
use App\Services\OracleProcedureService;
use Illuminate\Http\JsonResponse;

class SavedResourceController extends ApiController
{
    public function store(
        StudentResourceActionRequest $request,
        int $id,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        $studentId = (int) $request->validated('student_id');

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

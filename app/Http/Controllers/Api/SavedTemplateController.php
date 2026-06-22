<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Templates\StudentTemplateActionRequest;
use App\Models\SavedTemplate;
use App\Models\TemplatePurchase;
use App\Services\OracleProcedureService;
use Illuminate\Http\JsonResponse;

class SavedTemplateController extends ApiController
{
    public function store(
        StudentTemplateActionRequest $request,
        int $id,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        $studentId = (int) $request->validated('student_id');

        return $this->runOracleOperation(function () use ($studentId, $id, $oracleProcedures): SavedTemplate {
            $oracleProcedures->saveTemplate($studentId, $id);

            return SavedTemplate::query()
                ->with('template')
                ->where('student_id', $studentId)
                ->where('template_id', $id)
                ->firstOrFail();
        }, 'Template saved successfully', 201);
    }

    public function purchase(
        StudentTemplateActionRequest $request,
        int $id,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        $studentId = (int) $request->validated('student_id');

        return $this->runOracleOperation(function () use ($studentId, $id, $oracleProcedures): TemplatePurchase {
            $oracleProcedures->purchaseTemplate($studentId, $id);

            return TemplatePurchase::query()
                ->with('template')
                ->where('student_id', $studentId)
                ->where('template_id', $id)
                ->where('payment_status', 'PAID')
                ->orderByDesc('id')
                ->firstOrFail();
        }, 'Template purchased successfully', 201);
    }
}

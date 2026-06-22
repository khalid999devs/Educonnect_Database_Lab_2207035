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
        int $templateId,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        $studentId = (int) $request->validated('student_id');

        return $this->runOracleOperation(function () use ($studentId, $templateId, $oracleProcedures): SavedTemplate {
            $oracleProcedures->saveTemplate($studentId, $templateId);

            return SavedTemplate::query()
                ->with('template')
                ->where('student_id', $studentId)
                ->where('template_id', $templateId)
                ->firstOrFail();
        }, 'Template saved successfully', 201);
    }

    public function purchase(
        StudentTemplateActionRequest $request,
        int $templateId,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        $studentId = (int) $request->validated('student_id');

        return $this->runOracleOperation(function () use ($studentId, $templateId, $oracleProcedures): TemplatePurchase {
            $oracleProcedures->purchaseTemplate($studentId, $templateId);

            return TemplatePurchase::query()
                ->with('template')
                ->where('student_id', $studentId)
                ->where('template_id', $templateId)
                ->where('payment_status', 'PAID')
                ->orderByDesc('id')
                ->firstOrFail();
        }, 'Template purchased successfully', 201);
    }
}

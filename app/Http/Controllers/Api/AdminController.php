<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\ApprovalRequest;
use App\Models\Resource;
use App\Models\Template;
use App\Services\OracleProcedureService;
use Illuminate\Http\JsonResponse;

class AdminController extends ApiController
{
    public function approveResource(
        ApprovalRequest $request,
        int $resourceId,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        return $this->runOracleOperation(function () use ($request, $resourceId, $oracleProcedures): Resource {
            $oracleProcedures->approveResource(
                $resourceId,
                (int) $request->validated('admin_user_id'),
            );

            return Resource::query()->findOrFail($resourceId);
        }, 'Resource approved successfully');
    }

    public function approveTemplate(
        ApprovalRequest $request,
        int $templateId,
        OracleProcedureService $oracleProcedures,
    ): JsonResponse {
        return $this->runOracleOperation(function () use ($request, $templateId, $oracleProcedures): Template {
            $oracleProcedures->approveTemplate(
                $templateId,
                (int) $request->validated('admin_user_id'),
            );

            return Template::query()->findOrFail($templateId);
        }, 'Template approved successfully');
    }
}

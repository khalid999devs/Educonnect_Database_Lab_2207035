<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Templates\StoreTemplateRequest;
use App\Http\Requests\Templates\UpdateTemplateRequest;
use App\Models\Template;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class TemplateController extends ApiController
{
    public function index(): JsonResponse
    {
        $templates = Template::query()
            ->with(['category', 'university', 'academicField', 'creator'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return ApiResponse::success('Templates retrieved', $templates);
    }

    public function store(StoreTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] ??= $request->user()?->getAuthIdentifier();

        $template = Template::query()->create($data);

        return ApiResponse::success(
            'Template created successfully',
            $template->load(['category', 'university', 'academicField', 'creator']),
            201,
        );
    }

    public function show(int $id): JsonResponse
    {
        $template = Template::query()
            ->with(['category', 'university', 'academicField', 'creator', 'reviews.student.user'])
            ->findOrFail($id);

        return ApiResponse::success('Template retrieved', $template);
    }

    public function update(UpdateTemplateRequest $request, int $id): JsonResponse
    {
        $template = Template::query()->findOrFail($id);
        $template->update($request->validated());

        return ApiResponse::success(
            'Template updated successfully',
            $template->refresh()->load(['category', 'university', 'academicField', 'creator']),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        Template::query()->findOrFail($id)->delete();

        return ApiResponse::success('Template deleted successfully');
    }
}

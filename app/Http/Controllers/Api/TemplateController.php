<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Templates\StoreTemplateRequest;
use App\Http\Requests\Templates\TemplateIndexRequest;
use App\Http\Requests\Templates\UpdateTemplateRequest;
use App\Models\Template;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class TemplateController extends ApiController
{
    public function index(TemplateIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $search = strtolower($filters['search'] ?? '');

        $templates = Template::query()
            ->with(['category', 'university', 'academicField', 'creator'])
            ->when($filters['template_category_id'] ?? null, fn ($query, $categoryId) => $query->where('template_category_id', $categoryId))
            ->when($filters['university_id'] ?? null, fn ($query, $universityId) => $query->where('university_id', $universityId))
            ->when($filters['academic_field_id'] ?? null, fn ($query, $fieldId) => $query->where('academic_field_id', $fieldId))
            ->when(array_key_exists('is_paid', $filters), fn ($query) => $query->where('is_paid', (int) $filters['is_paid']))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]))
            ->orderByDesc('created_at')
            ->paginate($request->perPage())
            ->withQueryString();

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

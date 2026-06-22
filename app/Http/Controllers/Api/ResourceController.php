<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Resources\ResourceIndexRequest;
use App\Http\Requests\Resources\StoreResourceRequest;
use App\Http\Requests\Resources\UpdateResourceRequest;
use App\Models\Resource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ResourceController extends ApiController
{
    public function index(ResourceIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $search = strtolower($filters['search'] ?? '');

        $resources = Resource::query()
            ->with(['category', 'academicField', 'task', 'creator'])
            ->when($filters['resource_category_id'] ?? null, fn ($query, $categoryId) => $query->where('resource_category_id', $categoryId))
            ->when($filters['academic_field_id'] ?? null, fn ($query, $fieldId) => $query->where('academic_field_id', $fieldId))
            ->when($filters['task_id'] ?? null, fn ($query, $taskId) => $query->where('task_id', $taskId))
            ->when($filters['difficulty_level'] ?? null, fn ($query, $level) => $query->where('difficulty_level', $level))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]))
            ->orderByDesc('created_at')
            ->paginate($request->perPage())
            ->withQueryString();

        return ApiResponse::success('Resources retrieved', $resources);
    }

    public function store(StoreResourceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] ??= $request->user()?->getAuthIdentifier();

        $resource = Resource::query()->create($data);

        return ApiResponse::success(
            'Resource created successfully',
            $resource->load(['category', 'academicField', 'task', 'creator']),
            201,
        );
    }

    public function show(int $id): JsonResponse
    {
        $resource = Resource::query()
            ->with(['category', 'academicField', 'task', 'creator', 'reviews.student.user'])
            ->findOrFail($id);

        return ApiResponse::success('Resource retrieved', $resource);
    }

    public function update(UpdateResourceRequest $request, int $id): JsonResponse
    {
        $resource = Resource::query()->findOrFail($id);
        $resource->update($request->validated());

        return ApiResponse::success(
            'Resource updated successfully',
            $resource->refresh()->load(['category', 'academicField', 'task', 'creator']),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        Resource::query()->findOrFail($id)->delete();

        return ApiResponse::success('Resource deleted successfully');
    }
}

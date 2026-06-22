<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Tools\StoreToolRequest;
use App\Http\Requests\Tools\ToolIndexRequest;
use App\Http\Requests\Tools\UpdateToolRequest;
use App\Models\Tool;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ToolController extends ApiController
{
    public function index(ToolIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $search = strtolower($filters['search'] ?? '');

        $tools = Tool::query()
            ->with(['category', 'academicField', 'task'])
            ->when($filters['tool_category_id'] ?? null, fn ($query, $categoryId) => $query->where('tool_category_id', $categoryId))
            ->when($filters['academic_field_id'] ?? null, fn ($query, $fieldId) => $query->where('academic_field_id', $fieldId))
            ->when($filters['task_id'] ?? null, fn ($query, $taskId) => $query->where('task_id', $taskId))
            ->when(array_key_exists('is_free', $filters), fn ($query) => $query->where('is_free', (int) $filters['is_free']))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]))
            ->orderByDesc('created_at')
            ->paginate($request->perPage())
            ->withQueryString();

        return ApiResponse::success('Tools retrieved', $tools);
    }

    public function store(StoreToolRequest $request): JsonResponse
    {
        $tool = Tool::query()->create($request->validated());

        return ApiResponse::success(
            'Tool created successfully',
            $tool->load(['category', 'academicField', 'task']),
            201,
        );
    }

    public function show(int $id): JsonResponse
    {
        $tool = Tool::query()
            ->with(['category', 'academicField', 'task', 'reviews.student.user'])
            ->findOrFail($id);

        return ApiResponse::success('Tool retrieved', $tool);
    }

    public function update(UpdateToolRequest $request, int $id): JsonResponse
    {
        $tool = Tool::query()->findOrFail($id);
        $tool->update($request->validated());

        return ApiResponse::success(
            'Tool updated successfully',
            $tool->refresh()->load(['category', 'academicField', 'task']),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        Tool::query()->findOrFail($id)->delete();

        return ApiResponse::success('Tool deleted successfully');
    }
}

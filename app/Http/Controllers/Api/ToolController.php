<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Tools\StoreToolRequest;
use App\Http\Requests\Tools\UpdateToolRequest;
use App\Models\Tool;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ToolController extends ApiController
{
    public function index(): JsonResponse
    {
        $tools = Tool::query()
            ->with(['category', 'academicField', 'task'])
            ->orderByDesc('created_at')
            ->paginate(15);

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

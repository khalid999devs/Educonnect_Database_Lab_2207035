<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Resources\StoreResourceRequest;
use App\Http\Requests\Resources\UpdateResourceRequest;
use App\Models\Resource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ResourceController extends ApiController
{
    public function index(): JsonResponse
    {
        $resources = Resource::query()
            ->with(['category', 'academicField', 'task', 'creator'])
            ->orderByDesc('created_at')
            ->paginate(15);

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

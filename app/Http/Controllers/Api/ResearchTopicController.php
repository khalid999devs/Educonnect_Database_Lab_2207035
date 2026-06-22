<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Research\StoreResearchCollectionRequest;
use App\Http\Requests\Research\StoreResearchTopicRequest;
use App\Http\Requests\Research\UpdateResearchTopicRequest;
use App\Models\ResearchCollection;
use App\Models\ResearchTopic;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ResearchTopicController extends ApiController
{
    public function index(): JsonResponse
    {
        $topics = ResearchTopic::query()
            ->with(['student.user', 'academicField'])
            ->withCount('collections')
            ->orderByDesc('created_at')
            ->paginate(15);

        return ApiResponse::success('Research topics retrieved', $topics);
    }

    public function store(StoreResearchTopicRequest $request): JsonResponse
    {
        $topic = ResearchTopic::query()->create($request->validated());

        return ApiResponse::success(
            'Research topic created successfully',
            $topic->load(['student.user', 'academicField']),
            201,
        );
    }

    public function show(int $id): JsonResponse
    {
        $topic = ResearchTopic::query()
            ->with(['student.user', 'academicField', 'collections'])
            ->findOrFail($id);

        return ApiResponse::success('Research topic retrieved', $topic);
    }

    public function update(UpdateResearchTopicRequest $request, int $id): JsonResponse
    {
        $topic = ResearchTopic::query()->findOrFail($id);
        $topic->update($request->validated());

        return ApiResponse::success(
            'Research topic updated successfully',
            $topic->refresh()->load(['student.user', 'academicField', 'collections']),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        ResearchTopic::query()->findOrFail($id)->delete();

        return ApiResponse::success('Research topic deleted successfully');
    }

    public function collections(int $id): JsonResponse
    {
        $topic = ResearchTopic::query()->findOrFail($id);

        return ApiResponse::success(
            'Research collection items retrieved',
            $topic->collections()->orderByDesc('created_at')->get(),
        );
    }

    public function storeCollection(StoreResearchCollectionRequest $request, int $id): JsonResponse
    {
        ResearchTopic::query()->findOrFail($id);

        $collection = ResearchCollection::query()->create([
            ...$request->validated(),
            'research_topic_id' => $id,
        ]);

        return ApiResponse::success('Research collection item created successfully', $collection, 201);
    }
}

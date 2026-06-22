<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Http\Requests\Reviews\UpdateReviewRequest;
use App\Models\Review;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReviewController extends ApiController
{
    public function index(): JsonResponse
    {
        $reviews = Review::query()
            ->with(['student.user', 'reviewable'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return ApiResponse::success('Reviews retrieved', $reviews);
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::query()->create($request->validated());

        return ApiResponse::success(
            'Review created successfully',
            $review->load(['student.user', 'reviewable']),
            201,
        );
    }

    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = Review::query()->findOrFail($id);
        $review->update($request->validated());

        return ApiResponse::success(
            'Review updated successfully',
            $review->refresh()->load(['student.user', 'reviewable']),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        Review::query()->findOrFail($id)->delete();

        return ApiResponse::success('Review deleted successfully');
    }
}

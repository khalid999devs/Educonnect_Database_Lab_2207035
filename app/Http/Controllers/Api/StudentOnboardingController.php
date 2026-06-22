<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Students\StudentOnboardingRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Models\Student;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StudentOnboardingController extends ApiController
{
    public function store(StudentOnboardingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = (int) ($request->user()?->getAuthIdentifier() ?? $validated['user_id']);

        if (Student::query()->where('user_id', $userId)->exists()) {
            return ApiResponse::error('Student profile already exists', null, 409);
        }

        $student = DB::connection('oracle')->transaction(function () use ($validated, $userId): Student {
            $student = Student::query()->create([
                ...Arr::except($validated, ['user_id', 'preferences']),
                'user_id' => $userId,
            ]);

            if ($validated['preferences'] ?? []) {
                $student->preferences()->createMany($validated['preferences']);
            }

            return $student;
        });

        return ApiResponse::success(
            'Student onboarding completed successfully',
            $student->load(['user', 'university', 'academicField', 'preferences']),
            201,
        );
    }

    public function show(int $id): JsonResponse
    {
        $student = Student::query()
            ->with(['user', 'university', 'academicField', 'preferences'])
            ->withCount(['savedResources', 'savedTemplates', 'documents', 'researchTopics'])
            ->findOrFail($id);

        return ApiResponse::success('Student profile retrieved', $student);
    }

    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        $student = Student::query()->findOrFail($id);
        $student->update($request->validated());

        return ApiResponse::success(
            'Student profile updated successfully',
            $student->refresh()->load(['user', 'university', 'academicField', 'preferences']),
        );
    }
}

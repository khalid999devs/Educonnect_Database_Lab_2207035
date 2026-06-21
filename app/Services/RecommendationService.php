<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class RecommendationService
{
    /**
     * @return Collection<int, resource>
     */
    public function getForStudent(int $studentId, int $limit = 10): Collection
    {
        Student::query()->findOrFail($studentId);

        $resources = Resource::query()
            ->with(['category', 'academicField', 'task'])
            ->approved()
            ->select('resources.*')
            ->selectRaw(
                'fn_recommendation_score(?, resources.id) AS recommendation_score',
                [$studentId],
            )
            ->orderByDesc('recommendation_score')
            ->orderByDesc('average_rating')
            ->orderByDesc('save_count')
            ->limit(max(1, min($limit, 50)))
            ->get();

        $resources->each(function (Resource $resource): void {
            $resource->setAttribute(
                'recommendation_score',
                (int) round((float) $resource->getAttribute('recommendation_score')),
            );
        });

        return $resources;
    }
}

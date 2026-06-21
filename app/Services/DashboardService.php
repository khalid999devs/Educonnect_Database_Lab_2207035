<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\Student;

class DashboardService
{
    public function __construct(
        private readonly OracleProcedureService $oracleProcedures,
        private readonly RecommendationService $recommendations,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getForStudent(int $studentId): array
    {
        $student = Student::query()
            ->with(['user', 'university', 'academicField', 'preferences'])
            ->withCount([
                'savedResources',
                'savedTemplates',
                'documents',
                'researchTopics',
            ])
            ->findOrFail($studentId);

        $recentResources = Resource::query()
            ->with(['category', 'academicField', 'task'])
            ->approved()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'student_profile' => $student,
            'profile_completion' => $this->oracleProcedures->getProfileCompletion($studentId),
            'saved_resources_count' => (int) $student->saved_resources_count,
            'saved_templates_count' => (int) $student->saved_templates_count,
            'uploaded_documents_count' => (int) $student->documents_count,
            'research_topics_count' => (int) $student->research_topics_count,
            'recent_resources' => $recentResources,
            'recommendations' => $this->recommendations->getForStudent($studentId),
        ];
    }
}

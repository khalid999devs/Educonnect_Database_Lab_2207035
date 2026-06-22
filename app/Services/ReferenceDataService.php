<?php

namespace App\Services;

use App\Models\AcademicField;
use App\Models\AcademicTask;
use App\Models\ResourceCategory;
use App\Models\TemplateCategory;
use App\Models\ToolCategory;
use App\Models\University;

class ReferenceDataService
{
    /**
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        return [
            'universities' => University::query()
                ->select(['id', 'name', 'country', 'city'])
                ->orderBy('name')
                ->get(),
            'academic_fields' => AcademicField::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'academic_tasks' => AcademicTask::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'resource_categories' => ResourceCategory::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'tool_categories' => ToolCategory::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'template_categories' => TemplateCategory::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
        ];
    }
}

<?php

namespace App\Http\Requests\Resources;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Validation\Rule;

class ResourceIndexRequest extends IndexQueryRequest
{
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'search' => ['sometimes', 'string', 'max:255'],
            'resource_category_id' => ['sometimes', 'integer', Rule::exists('resource_categories', 'id')],
            'academic_field_id' => ['sometimes', 'integer', Rule::exists('academic_fields', 'id')],
            'task_id' => ['sometimes', 'integer', Rule::exists('academic_tasks', 'id')],
            'difficulty_level' => ['sometimes', Rule::in(['BEGINNER', 'INTERMEDIATE', 'ADVANCED'])],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->normalizeUppercase(['difficulty_level', 'status']);
    }
}

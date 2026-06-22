<?php

namespace App\Http\Requests\Resources;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'resource_category_id' => ['sometimes', 'integer', Rule::exists('resource_categories', 'id')],
            'academic_field_id' => ['sometimes', 'nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'task_id' => ['sometimes', 'nullable', 'integer', Rule::exists('academic_tasks', 'id')],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'resource_url' => ['sometimes', 'url', 'max:1000'],
            'difficulty_level' => ['sometimes', Rule::in(['BEGINNER', 'INTERMEDIATE', 'ADVANCED'])],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $values = [];

        foreach (['difficulty_level', 'status'] as $field) {
            if ($this->has($field)) {
                $values[$field] = strtoupper((string) $this->input($field));
            }
        }

        $this->merge($values);
    }
}

<?php

namespace App\Http\Requests\Resources;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'resource_category_id' => ['required', 'integer', Rule::exists('resource_categories', 'id')],
            'academic_field_id' => ['nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'task_id' => ['nullable', 'integer', Rule::exists('academic_tasks', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'resource_url' => ['required', 'url', 'max:1000'],
            'difficulty_level' => ['required', Rule::in(['BEGINNER', 'INTERMEDIATE', 'ADVANCED'])],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
            'created_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'difficulty_level' => strtoupper((string) $this->input('difficulty_level')),
            'status' => strtoupper((string) $this->input('status', 'PENDING')),
        ]);
    }
}

<?php

namespace App\Http\Requests\Tools;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateToolRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'tool_category_id' => ['sometimes', 'integer', Rule::exists('tool_categories', 'id')],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'website_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'academic_field_id' => ['sometimes', 'nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'task_id' => ['sometimes', 'nullable', 'integer', Rule::exists('academic_tasks', 'id')],
            'is_free' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => strtoupper((string) $this->input('status')),
            ]);
        }
    }
}

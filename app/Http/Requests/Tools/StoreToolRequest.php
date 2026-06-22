<?php

namespace App\Http\Requests\Tools;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreToolRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'tool_category_id' => ['required', 'integer', Rule::exists('tool_categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'academic_field_id' => ['nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'task_id' => ['nullable', 'integer', Rule::exists('academic_tasks', 'id')],
            'is_free' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => strtoupper((string) $this->input('status', 'PENDING')),
        ]);
    }
}

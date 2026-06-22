<?php

namespace App\Http\Requests\Templates;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreTemplateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'template_category_id' => ['required', 'integer', Rule::exists('template_categories', 'id')],
            'university_id' => ['nullable', 'integer', Rule::exists('universities', 'id')],
            'academic_field_id' => ['nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'template_url' => ['required', 'url', 'max:1000'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_paid' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
            'created_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => strtoupper((string) $this->input('status', 'PENDING')),
        ]);
    }
}

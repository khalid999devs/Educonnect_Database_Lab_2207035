<?php

namespace App\Http\Requests\Templates;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateTemplateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'template_category_id' => ['sometimes', 'integer', Rule::exists('template_categories', 'id')],
            'university_id' => ['sometimes', 'nullable', 'integer', Rule::exists('universities', 'id')],
            'academic_field_id' => ['sometimes', 'nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'template_url' => ['sometimes', 'url', 'max:1000'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_paid' => ['sometimes', 'boolean'],
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

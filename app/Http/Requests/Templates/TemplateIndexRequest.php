<?php

namespace App\Http\Requests\Templates;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Validation\Rule;

class TemplateIndexRequest extends IndexQueryRequest
{
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'search' => ['sometimes', 'string', 'max:255'],
            'template_category_id' => ['sometimes', 'integer', Rule::exists('template_categories', 'id')],
            'university_id' => ['sometimes', 'integer', Rule::exists('universities', 'id')],
            'academic_field_id' => ['sometimes', 'integer', Rule::exists('academic_fields', 'id')],
            'is_paid' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->normalizeUppercase(['status']);
    }
}

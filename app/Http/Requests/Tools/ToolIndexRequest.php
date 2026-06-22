<?php

namespace App\Http\Requests\Tools;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Validation\Rule;

class ToolIndexRequest extends IndexQueryRequest
{
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'search' => ['sometimes', 'string', 'max:255'],
            'tool_category_id' => ['sometimes', 'integer', Rule::exists('tool_categories', 'id')],
            'academic_field_id' => ['sometimes', 'integer', Rule::exists('academic_fields', 'id')],
            'task_id' => ['sometimes', 'integer', Rule::exists('academic_tasks', 'id')],
            'is_free' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->normalizeUppercase(['status']);
    }
}

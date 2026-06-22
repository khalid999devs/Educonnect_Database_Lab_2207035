<?php

namespace App\Http\Requests\Research;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Validation\Rule;

class ResearchTopicIndexRequest extends IndexQueryRequest
{
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'student_id' => ['sometimes', 'integer', Rule::exists('students', 'id')],
            'academic_field_id' => ['sometimes', 'integer', Rule::exists('academic_fields', 'id')],
            'search' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['IDEA', 'READING', 'IN_PROGRESS', 'COMPLETED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->normalizeUppercase(['status']);
    }
}

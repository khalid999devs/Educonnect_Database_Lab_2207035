<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Validation\Rule;

class DocumentIndexRequest extends IndexQueryRequest
{
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'student_id' => ['sometimes', 'integer', Rule::exists('students', 'id')],
            'search' => ['sometimes', 'string', 'max:255'],
            'document_type' => ['sometimes', Rule::in(['CURRICULUM', 'ROUTINE', 'SYLLABUS', 'ASSIGNMENT', 'LAB_FILE', 'RESEARCH_PAPER', 'COURSE_OUTLINE', 'OTHER'])],
            'status' => ['sometimes', Rule::in(['UPLOADED', 'EXTRACTED', 'FAILED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->normalizeUppercase(['document_type', 'status']);
    }
}

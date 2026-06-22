<?php

namespace App\Http\Requests\Reviews;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Validation\Rule;

class ReviewIndexRequest extends IndexQueryRequest
{
    public function rules(): array
    {
        return [
            ...$this->paginationRules(),
            'student_id' => ['sometimes', 'integer', Rule::exists('students', 'id')],
            'reviewable_type' => ['sometimes', Rule::in(['RESOURCE', 'TEMPLATE', 'TOOL'])],
            'reviewable_id' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->normalizeUppercase(['reviewable_type']);
    }
}

<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicDocumentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'document_type' => ['sometimes', Rule::in(['CURRICULUM', 'ROUTINE', 'SYLLABUS', 'ASSIGNMENT', 'LAB_FILE', 'RESEARCH_PAPER', 'COURSE_OUTLINE', 'OTHER'])],
            'file_name' => ['sometimes', 'string', 'max:255'],
            'file_path' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'file_mime_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['UPLOADED', 'EXTRACTED', 'FAILED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $values = [];

        foreach (['document_type', 'status'] as $field) {
            if ($this->has($field)) {
                $values[$field] = strtoupper((string) $this->input($field));
            }
        }

        $this->merge($values);
    }
}

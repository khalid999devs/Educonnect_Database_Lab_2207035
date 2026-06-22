<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicDocumentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', Rule::in(['CURRICULUM', 'ROUTINE', 'SYLLABUS', 'ASSIGNMENT', 'LAB_FILE', 'RESEARCH_PAPER', 'COURSE_OUTLINE', 'OTHER'])],
            'file_name' => ['required', 'string', 'max:255'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'file_mime_type' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['UPLOADED', 'EXTRACTED', 'FAILED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'document_type' => strtoupper((string) $this->input('document_type')),
            'status' => strtoupper((string) $this->input('status', 'UPLOADED')),
        ]);
    }
}

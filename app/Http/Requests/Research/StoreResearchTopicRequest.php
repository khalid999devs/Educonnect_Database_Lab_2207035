<?php

namespace App\Http\Requests\Research;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreResearchTopicRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'academic_field_id' => ['nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'status' => ['sometimes', Rule::in(['IDEA', 'READING', 'IN_PROGRESS', 'COMPLETED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => strtoupper((string) $this->input('status', 'IDEA')),
        ]);
    }
}

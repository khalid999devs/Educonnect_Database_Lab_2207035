<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class AddExtractedDataRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
            'data_type' => ['required', Rule::in(['SUBJECT', 'TOPIC', 'DEADLINE', 'ROUTINE_SLOT', 'RESOURCE_NAME', 'RESEARCH_AREA', 'KEYWORD'])],
            'data_key' => ['required', 'string', 'max:255'],
            'data_value' => ['nullable', 'string'],
            'confidence_score' => ['required', 'numeric', 'between:0,1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'data_type' => strtoupper((string) $this->input('data_type')),
        ]);
    }
}

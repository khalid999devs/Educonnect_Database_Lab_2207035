<?php

namespace App\Http\Requests\Students;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'university_id' => ['sometimes', 'integer', Rule::exists('universities', 'id')],
            'academic_field_id' => ['sometimes', 'integer', Rule::exists('academic_fields', 'id')],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'semester' => ['sometimes', 'nullable', 'string', 'max:50'],
            'skill_level' => ['sometimes', Rule::in(['BEGINNER', 'INTERMEDIATE', 'ADVANCED'])],
            'bio' => ['sometimes', 'nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('skill_level')) {
            $this->merge([
                'skill_level' => strtoupper((string) $this->input('skill_level')),
            ]);
        }
    }
}

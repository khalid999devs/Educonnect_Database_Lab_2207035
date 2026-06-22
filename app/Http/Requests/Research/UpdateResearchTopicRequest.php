<?php

namespace App\Http\Requests\Research;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateResearchTopicRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'academic_field_id' => ['sometimes', 'nullable', 'integer', Rule::exists('academic_fields', 'id')],
            'status' => ['sometimes', Rule::in(['IDEA', 'READING', 'IN_PROGRESS', 'COMPLETED'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => strtoupper((string) $this->input('status')),
            ]);
        }
    }
}

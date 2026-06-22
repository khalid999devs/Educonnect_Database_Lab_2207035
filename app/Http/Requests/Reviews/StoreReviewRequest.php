<?php

namespace App\Http\Requests\Reviews;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
            'reviewable_type' => ['required', Rule::in(['RESOURCE', 'TEMPLATE', 'TOOL'])],
            'reviewable_id' => ['required', 'integer', Rule::exists($this->reviewableTable(), 'id')],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment_text' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reviewable_type' => strtoupper((string) $this->input('reviewable_type')),
        ]);
    }

    private function reviewableTable(): string
    {
        return match ($this->input('reviewable_type')) {
            'TEMPLATE' => 'templates',
            'TOOL' => 'tools',
            default => 'resources',
        };
    }
}

<?php

namespace App\Http\Requests\Reviews;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateReviewRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'reviewable_type' => ['sometimes', 'required_with:reviewable_id', Rule::in(['RESOURCE', 'TEMPLATE', 'TOOL'])],
            'reviewable_id' => ['sometimes', 'integer', Rule::exists($this->reviewableTable(), 'id')],
            'rating' => ['sometimes', 'integer', 'between:1,5'],
            'comment_text' => ['sometimes', 'nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('reviewable_type')) {
            $this->merge([
                'reviewable_type' => strtoupper((string) $this->input('reviewable_type')),
            ]);
        }
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

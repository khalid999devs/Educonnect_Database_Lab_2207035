<?php

namespace App\Http\Requests\Research;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreResearchCollectionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'collection_type' => ['required', Rule::in(['PAPER', 'DATASET', 'LINK', 'NOTE', 'VIDEO'])],
            'resource_url' => ['nullable', 'url', 'max:1000'],
            'summary' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'reading_status' => ['sometimes', Rule::in(['TO_READ', 'READING', 'READ', 'IMPORTANT'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'collection_type' => strtoupper((string) $this->input('collection_type')),
            'reading_status' => strtoupper((string) $this->input('reading_status', 'TO_READ')),
        ]);
    }
}

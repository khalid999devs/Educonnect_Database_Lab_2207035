<?php

namespace App\Http\Requests;

abstract class IndexQueryRequest extends ApiFormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    protected function paginationRules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search')) {
            $this->merge([
                'search' => trim((string) $this->input('search')),
            ]);
        }
    }

    /**
     * @param  array<int, string>  $fields
     */
    protected function normalizeUppercase(array $fields): void
    {
        $values = [];

        foreach ($fields as $field) {
            if ($this->has($field)) {
                $values[$field] = strtoupper((string) $this->input($field));
            }
        }

        $this->merge($values);
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 15);
    }
}

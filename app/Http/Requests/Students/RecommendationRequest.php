<?php

namespace App\Http\Requests\Students;

use App\Http\Requests\ApiFormRequest;

class RecommendationRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'limit' => ['sometimes', 'integer', 'between:1,50'],
        ];
    }
}

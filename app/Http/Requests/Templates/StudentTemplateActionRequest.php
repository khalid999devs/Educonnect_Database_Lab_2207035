<?php

namespace App\Http\Requests\Templates;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StudentTemplateActionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => [$this->user() ? 'nullable' : 'required', 'integer', Rule::exists('students', 'id')],
        ];
    }
}

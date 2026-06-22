<?php

namespace App\Http\Requests\Resources;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StudentResourceActionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
        ];
    }
}

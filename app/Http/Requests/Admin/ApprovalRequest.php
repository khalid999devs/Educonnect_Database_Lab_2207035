<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class ApprovalRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'admin_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', 'ADMIN')
                    ->where('status', 'ACTIVE')),
            ],
        ];
    }
}

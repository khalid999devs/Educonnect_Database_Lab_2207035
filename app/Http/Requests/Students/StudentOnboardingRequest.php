<?php

namespace App\Http\Requests\Students;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StudentOnboardingRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => [$this->user() ? 'nullable' : 'required', 'integer', Rule::exists('users', 'id')],
            'university_id' => ['required', 'integer', Rule::exists('universities', 'id')],
            'academic_field_id' => ['required', 'integer', Rule::exists('academic_fields', 'id')],
            'department' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:50'],
            'skill_level' => ['required', Rule::in(['BEGINNER', 'INTERMEDIATE', 'ADVANCED'])],
            'bio' => ['nullable', 'string'],
            'preferences' => ['sometimes', 'array'],
            'preferences.*.goal_type' => ['required', Rule::in(['ASSIGNMENT', 'RESEARCH', 'LAB', 'CODING', 'EXAM', 'PROJECT'])],
            'preferences.*.preference_key' => ['required', 'string', 'max:255'],
            'preferences.*.preference_value' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $preferences = collect($this->input('preferences', []))
            ->map(function (mixed $preference): mixed {
                if (! is_array($preference)) {
                    return $preference;
                }

                $preference['goal_type'] = strtoupper((string) ($preference['goal_type'] ?? ''));

                return $preference;
            })
            ->all();

        $this->merge([
            'skill_level' => strtoupper((string) $this->input('skill_level')),
            'preferences' => $preferences,
        ]);
    }
}

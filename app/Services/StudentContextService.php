<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;

class StudentContextService
{
    public function forUser(User $user): ?Student
    {
        if (! $user->relationLoaded('student')) {
            $user->load('student');
        }

        return $user->getRelation('student');
    }
}

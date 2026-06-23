<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\User;
use App\Services\StudentContextService;
use Tests\TestCase;

class StudentContextServiceTest extends TestCase
{
    public function test_it_returns_the_loaded_student_context(): void
    {
        $student = new Student;
        $student->id = 4;
        $user = new User;
        $user->setRelation('student', $student);

        $this->assertSame($student, (new StudentContextService)->forUser($user));
    }

    public function test_it_returns_null_when_profile_is_not_created(): void
    {
        $user = new User;
        $user->setRelation('student', null);

        $this->assertNull((new StudentContextService)->forUser($user));
    }
}

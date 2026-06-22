<?php

namespace Tests\Unit;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Resources\StoreResourceRequest;
use App\Http\Requests\Students\StudentOnboardingRequest;
use ReflectionMethod;
use Tests\TestCase;

class FormRequestNormalizationTest extends TestCase
{
    public function test_login_request_normalizes_email(): void
    {
        $request = LoginRequest::create('/', 'POST', [
            'email' => 'STUDENT@EXAMPLE.COM',
        ]);

        $this->prepare($request);

        $this->assertSame('student@example.com', $request->input('email'));
    }

    public function test_register_request_normalizes_email_and_role(): void
    {
        $request = RegisterRequest::create('/', 'POST', [
            'email' => 'STUDENT@EXAMPLE.COM',
            'role' => 'student',
        ]);

        $this->prepare($request);

        $this->assertSame('student@example.com', $request->input('email'));
        $this->assertSame('STUDENT', $request->input('role'));
    }

    public function test_resource_request_normalizes_oracle_enum_values(): void
    {
        $request = StoreResourceRequest::create('/', 'POST', [
            'difficulty_level' => 'intermediate',
        ]);

        $this->prepare($request);

        $this->assertSame('INTERMEDIATE', $request->input('difficulty_level'));
        $this->assertSame('PENDING', $request->input('status'));
    }

    public function test_student_request_normalizes_nested_preference_goal_types(): void
    {
        $request = StudentOnboardingRequest::create('/', 'POST', [
            'skill_level' => 'advanced',
            'preferences' => [
                ['goal_type' => 'research'],
            ],
        ]);

        $this->prepare($request);

        $this->assertSame('ADVANCED', $request->input('skill_level'));
        $this->assertSame('RESEARCH', $request->input('preferences.0.goal_type'));
    }

    private function prepare(object $request): void
    {
        (new ReflectionMethod($request, 'prepareForValidation'))->invoke($request);
    }
}

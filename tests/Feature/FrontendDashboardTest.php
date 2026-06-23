<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class FrontendDashboardTest extends TestCase
{
    public function test_user_without_student_profile_is_sent_to_onboarding(): void
    {
        $user = $this->studentUser();
        $user->setRelation('student', null);

        $this->actingAs($user)->get('/app')->assertRedirect(route('onboarding'));
        $this->get('/app/onboarding')
            ->assertOk()
            ->assertSee('Tell us what you are studying')
            ->assertSee('data-onboarding-form', false);
    }

    public function test_onboarded_student_is_sent_to_dashboard_from_onboarding(): void
    {
        $user = $this->studentUser();
        $user->setRelation('student', $this->student());

        $this->actingAs($user)
            ->get('/app/onboarding')
            ->assertRedirect(route('workspace'));
    }

    public function test_dashboard_renders_live_data_hooks_for_student(): void
    {
        $user = $this->studentUser();
        $user->setRelation('student', $this->student());

        $this->actingAs($user)
            ->get('/app')
            ->assertOk()
            ->assertSee('data-dashboard', false)
            ->assertSee('data-student-id="11"', false)
            ->assertSee('data-dashboard-loading', false)
            ->assertSee('data-recommendation-list', false)
            ->assertSee('data-recent-list', false);
    }

    private function studentUser(): User
    {
        $user = new User([
            'name' => 'Ada Student',
            'email' => 'ada@example.com',
            'role' => 'STUDENT',
            'status' => 'ACTIVE',
        ]);
        $user->id = 7;
        $user->exists = true;

        return $user;
    }

    private function student(): Student
    {
        $student = new Student;
        $student->id = 11;
        $student->exists = true;

        return $student;
    }
}

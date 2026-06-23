<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FrontendCatalogTest extends TestCase
{
    public function test_onboarded_student_can_open_each_catalog(): void
    {
        $user = $this->userWithStudent();

        foreach (['resources', 'tools', 'templates'] as $catalog) {
            $this->actingAs($user)
                ->get(route('catalog.index', ['catalog' => $catalog]))
                ->assertOk()
                ->assertSee("data-catalog-type=\"{$catalog}\"", false)
                ->assertSee('data-student-id="9"', false)
                ->assertSee('data-catalog-filters', false)
                ->assertSee('data-catalog-dialog', false);
        }
    }

    public function test_catalog_requires_completed_student_profile(): void
    {
        $user = $this->userWithStudent();
        $user->setRelation('student', null);

        $this->actingAs($user)
            ->get(route('catalog.index', ['catalog' => 'resources']))
            ->assertRedirect(route('onboarding'));
    }

    public function test_unknown_catalog_is_not_a_valid_route(): void
    {
        $this->actingAs($this->userWithStudent())
            ->get('/app/unknown-catalog')
            ->assertNotFound();
    }

    public function test_catalog_route_is_session_protected(): void
    {
        $route = Route::getRoutes()->getByName('catalog.index');

        $this->assertContains('auth', $route->gatherMiddleware());
        $this->get('/app/resources')->assertRedirect(route('login'));
    }

    private function userWithStudent(): User
    {
        $student = new Student;
        $student->id = 9;
        $student->exists = true;

        $user = new User([
            'name' => 'Ada Student',
            'email' => 'ada@example.com',
            'role' => 'STUDENT',
            'status' => 'ACTIVE',
        ]);
        $user->id = 7;
        $user->exists = true;
        $user->setRelation('student', $student);

        return $user;
    }
}

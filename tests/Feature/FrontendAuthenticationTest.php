<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FrontendAuthenticationTest extends TestCase
{
    public function test_guest_authentication_pages_are_available(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('data-auth-form="login"', false)
            ->assertSee(route('register'));

        $this->get('/register')
            ->assertOk()
            ->assertSee('data-auth-form="register"', false)
            ->assertSee('name="role" value="STUDENT"', false)
            ->assertSee(route('login'));
    }

    public function test_workspace_requires_an_authenticated_session(): void
    {
        $this->get('/app')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_workspace(): void
    {
        $user = new User([
            'name' => 'Ada Student',
            'email' => 'ada@example.com',
            'role' => 'STUDENT',
            'status' => 'ACTIVE',
        ]);
        $user->id = 7;
        $user->exists = true;

        $this->actingAs($user)
            ->get('/app')
            ->assertOk()
            ->assertSee('Welcome, Ada')
            ->assertSee('ada@example.com')
            ->assertSee('data-logout-button', false);
    }

    public function test_authenticated_user_is_redirected_away_from_guest_pages(): void
    {
        $user = new User(['name' => 'Ada Student', 'role' => 'STUDENT', 'status' => 'ACTIVE']);
        $user->id = 7;
        $user->exists = true;

        $this->actingAs($user)->get('/login')->assertRedirect(route('workspace'));
        $this->get('/register')->assertRedirect(route('workspace'));
    }

    public function test_frontend_routes_use_expected_authentication_middleware(): void
    {
        $this->assertContains('guest', Route::getRoutes()->getByName('login')->gatherMiddleware());
        $this->assertContains('guest', Route::getRoutes()->getByName('register')->gatherMiddleware());
        $this->assertContains('auth', Route::getRoutes()->getByName('workspace')->gatherMiddleware());
    }

    public function test_api_logout_invalidates_the_authenticated_session(): void
    {
        $user = new User(['name' => 'Ada Student', 'role' => 'STUDENT', 'status' => 'ACTIVE']);
        $user->id = 7;
        $user->exists = true;

        $this->actingAs($user)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Logout successful',
            ]);

        $this->assertGuest();
    }
}

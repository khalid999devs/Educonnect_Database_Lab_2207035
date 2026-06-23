<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class FrontendFoundationTest extends TestCase
{
    public function test_root_redirects_to_login_page(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_login_page_renders_frontend_foundation(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Sign in to your account')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('assets/css/tokens.css', false)
            ->assertSee('assets/js/app.js', false)
            ->assertSee('assets/images/auth-workspace.png', false);
    }

    public function test_authenticated_layout_can_be_rendered(): void
    {
        $html = Blade::render(<<<'BLADE'
            @extends('layouts.app')
            @section('title', 'Workspace')
            @section('content')<h1>Workspace</h1>@endsection
        BLADE);

        $this->assertStringContainsString('class="app-shell"', $html);
        $this->assertStringContainsString('<h1>Workspace</h1>', $html);
    }

    public function test_frontend_assets_exist_in_public_directory(): void
    {
        $this->assertFileExists(public_path('assets/css/tokens.css'));
        $this->assertFileExists(public_path('assets/css/auth.css'));
        $this->assertFileExists(public_path('assets/css/catalog.css'));
        $this->assertFileExists(public_path('assets/css/onboarding.css'));
        $this->assertFileExists(public_path('assets/css/workspace.css'));
        $this->assertFileExists(public_path('assets/js/core/api-client.js'));
        $this->assertFileExists(public_path('assets/js/features/auth.js'));
        $this->assertFileExists(public_path('assets/js/features/catalog.js'));
        $this->assertFileExists(public_path('assets/js/features/catalog-config.js'));
        $this->assertFileExists(public_path('assets/js/features/catalog-renderer.js'));
        $this->assertFileExists(public_path('assets/js/features/dashboard.js'));
        $this->assertFileExists(public_path('assets/js/features/onboarding.js'));
        $this->assertFileExists(public_path('assets/images/auth-workspace.png'));
    }
}

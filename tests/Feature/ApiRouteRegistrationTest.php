<?php

namespace Tests\Feature;

use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiRouteRegistrationTest extends TestCase
{
    public function test_all_required_api_v1_routes_are_registered(): void
    {
        $actualRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (IlluminateRoute $route): bool => str_starts_with($route->uri(), 'api/v1/'))
            ->flatMap(fn (IlluminateRoute $route): array => collect($route->methods())
                ->reject(fn (string $method): bool => $method === 'HEAD')
                ->mapWithKeys(fn (string $method): array => ["{$method} {$route->uri()}" => $route->getActionName()])
                ->all())
            ->sortKeys()
            ->all();

        $expectedRoutes = collect($this->requiredRoutes())->sortKeys()->all();

        $this->assertSame($expectedRoutes, $actualRoutes);
    }

    public function test_authenticated_user_routes_have_session_and_auth_middleware(): void
    {
        foreach (['api.v1.auth.me', 'api.v1.auth.logout'] as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertContains('Illuminate\\Session\\Middleware\\StartSession', $route->gatherMiddleware());
            $this->assertContains('auth', $route->gatherMiddleware());
        }
    }

    public function test_authenticated_user_routes_require_login(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors' => [],
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function requiredRoutes(): array
    {
        return [
            'GET api/v1/health/database' => 'App\\Http\\Controllers\\Api\\DatabaseHealthController',
            'GET api/v1/reference-data' => 'App\\Http\\Controllers\\Api\\ReferenceDataController',
            'POST api/v1/auth/register' => 'App\\Http\\Controllers\\Api\\AuthController@register',
            'POST api/v1/auth/login' => 'App\\Http\\Controllers\\Api\\AuthController@login',
            'GET api/v1/auth/me' => 'App\\Http\\Controllers\\Api\\AuthController@me',
            'POST api/v1/auth/logout' => 'App\\Http\\Controllers\\Api\\AuthController@logout',
            'POST api/v1/students/onboarding' => 'App\\Http\\Controllers\\Api\\StudentOnboardingController@store',
            'GET api/v1/students/{id}' => 'App\\Http\\Controllers\\Api\\StudentOnboardingController@show',
            'PUT api/v1/students/{id}' => 'App\\Http\\Controllers\\Api\\StudentOnboardingController@update',
            'GET api/v1/documents' => 'App\\Http\\Controllers\\Api\\AcademicDocumentController@index',
            'POST api/v1/documents' => 'App\\Http\\Controllers\\Api\\AcademicDocumentController@store',
            'GET api/v1/documents/{id}' => 'App\\Http\\Controllers\\Api\\AcademicDocumentController@show',
            'PUT api/v1/documents/{id}' => 'App\\Http\\Controllers\\Api\\AcademicDocumentController@update',
            'DELETE api/v1/documents/{id}' => 'App\\Http\\Controllers\\Api\\AcademicDocumentController@destroy',
            'POST api/v1/documents/{id}/extracted-data' => 'App\\Http\\Controllers\\Api\\AcademicDocumentController@addExtractedData',
            'GET api/v1/resources' => 'App\\Http\\Controllers\\Api\\ResourceController@index',
            'POST api/v1/resources' => 'App\\Http\\Controllers\\Api\\ResourceController@store',
            'GET api/v1/resources/{id}' => 'App\\Http\\Controllers\\Api\\ResourceController@show',
            'PUT api/v1/resources/{id}' => 'App\\Http\\Controllers\\Api\\ResourceController@update',
            'DELETE api/v1/resources/{id}' => 'App\\Http\\Controllers\\Api\\ResourceController@destroy',
            'POST api/v1/resources/{id}/save' => 'App\\Http\\Controllers\\Api\\SavedResourceController@store',
            'POST api/v1/resources/{id}/approve' => 'App\\Http\\Controllers\\Api\\AdminController@approveResource',
            'GET api/v1/tools' => 'App\\Http\\Controllers\\Api\\ToolController@index',
            'POST api/v1/tools' => 'App\\Http\\Controllers\\Api\\ToolController@store',
            'GET api/v1/tools/{id}' => 'App\\Http\\Controllers\\Api\\ToolController@show',
            'PUT api/v1/tools/{id}' => 'App\\Http\\Controllers\\Api\\ToolController@update',
            'DELETE api/v1/tools/{id}' => 'App\\Http\\Controllers\\Api\\ToolController@destroy',
            'GET api/v1/templates' => 'App\\Http\\Controllers\\Api\\TemplateController@index',
            'POST api/v1/templates' => 'App\\Http\\Controllers\\Api\\TemplateController@store',
            'GET api/v1/templates/{id}' => 'App\\Http\\Controllers\\Api\\TemplateController@show',
            'PUT api/v1/templates/{id}' => 'App\\Http\\Controllers\\Api\\TemplateController@update',
            'DELETE api/v1/templates/{id}' => 'App\\Http\\Controllers\\Api\\TemplateController@destroy',
            'POST api/v1/templates/{id}/save' => 'App\\Http\\Controllers\\Api\\SavedTemplateController@store',
            'POST api/v1/templates/{id}/purchase' => 'App\\Http\\Controllers\\Api\\SavedTemplateController@purchase',
            'POST api/v1/templates/{id}/approve' => 'App\\Http\\Controllers\\Api\\AdminController@approveTemplate',
            'GET api/v1/research-topics' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@index',
            'POST api/v1/research-topics' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@store',
            'GET api/v1/research-topics/{id}' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@show',
            'PUT api/v1/research-topics/{id}' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@update',
            'DELETE api/v1/research-topics/{id}' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@destroy',
            'GET api/v1/research-topics/{id}/collections' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@collections',
            'POST api/v1/research-topics/{id}/collections' => 'App\\Http\\Controllers\\Api\\ResearchTopicController@storeCollection',
            'GET api/v1/reviews' => 'App\\Http\\Controllers\\Api\\ReviewController@index',
            'POST api/v1/reviews' => 'App\\Http\\Controllers\\Api\\ReviewController@store',
            'PUT api/v1/reviews/{id}' => 'App\\Http\\Controllers\\Api\\ReviewController@update',
            'DELETE api/v1/reviews/{id}' => 'App\\Http\\Controllers\\Api\\ReviewController@destroy',
            'GET api/v1/dashboard/student/{id}' => 'App\\Http\\Controllers\\Api\\DashboardController@show',
            'GET api/v1/recommendations/student/{id}' => 'App\\Http\\Controllers\\Api\\RecommendationController@index',
        ];
    }
}

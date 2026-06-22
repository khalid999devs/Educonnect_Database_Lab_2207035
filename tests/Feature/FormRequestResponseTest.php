<?php

namespace Tests\Feature;

use App\Http\Requests\Auth\RegisterRequest;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FormRequestResponseTest extends TestCase
{
    public function test_form_request_returns_the_standard_validation_error_shape(): void
    {
        Route::post('/_test/register-request', function (RegisterRequest $request) {
            return ApiResponse::success('Validated', $request->validated());
        });

        $response = $this->postJson('/_test/register-request', []);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role'], 'errors');
    }

    public function test_missing_api_models_return_the_standard_error_shape(): void
    {
        Route::get('/api/_test/missing-model', function () {
            throw new ModelNotFoundException;
        });

        $this->getJson('/api/_test/missing-model')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Requested record not found',
            ]);
    }
}

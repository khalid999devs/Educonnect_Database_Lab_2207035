<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            ...$request->validated(),
            'status' => 'ACTIVE',
        ]);

        return ApiResponse::success('User registered successfully', $user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt([
            ...$request->validated(),
            'status' => 'ACTIVE',
        ])) {
            return ApiResponse::error('Invalid credentials', null, 401);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return ApiResponse::success('Login successful', $request->user());
    }

    public function me(Request $request): JsonResponse
    {
        if (! $request->user()) {
            return ApiResponse::error('Unauthenticated', null, 401);
        }

        return ApiResponse::success('Authenticated user retrieved', $request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return ApiResponse::success('Logout successful');
    }
}

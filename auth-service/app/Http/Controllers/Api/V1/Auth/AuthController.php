<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTOs\Auth\LoginUserData;
use App\DTOs\Auth\RegisterUserData;
use App\Exceptions\UserAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $payload = $this->authService->register(
                RegisterUserData::fromRequest($request),
            );

            return $this->successResponse([
                'user' => new UserResource($payload['user']),
                'access_token' => $payload['access_token'],
                'token_type' => $payload['token_type'],
                'expires_in' => $payload['expires_in'],
            ], 'User registered successfully.', 201);
        } catch (UserAlreadyExistsException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $this->authService->login(
            LoginUserData::fromRequest($request),
        );

        if (! $payload) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        return $this->successResponse([
            'user' => new UserResource($payload['user']),
            'access_token' => $payload['access_token'],
            'token_type' => $payload['token_type'],
            'expires_in' => $payload['expires_in'],
        ], 'Login successful.');
    }

    public function me(): JsonResponse
    {
        return $this->successResponse([
            'user' => new UserResource(auth('api')->user()),
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(message: 'Logged out successfully.');
    }

    public function refresh(): JsonResponse
    {
        return $this->successResponse(
            $this->authService->refresh(),
            'Token refreshed successfully.',
        );
    }
}

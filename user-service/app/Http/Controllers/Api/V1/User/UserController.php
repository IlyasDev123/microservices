<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DTOs\User\UpdateUserData;
use App\Exceptions\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserService $userService,
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->userService->list(perPage: 15);

        return $this->successResponse([
            'users' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse([
                'user' => new UserResource($this->userService->show($id)),
            ]);
        } catch (UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function profile(): JsonResponse
    {
        return $this->successResponse([
            'user' => new UserResource(auth('api')->user()),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->update(
                auth('api')->id(),
                UpdateUserData::fromRequest($request),
            );

            return $this->successResponse([
                'user' => new UserResource($user),
            ], 'Profile updated successfully.');
        } catch (UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function update(UpdateProfileRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->update(
                $id,
                UpdateUserData::fromRequest($request),
            );

            return $this->successResponse([
                'user' => new UserResource($user),
            ], 'User updated successfully.');
        } catch (UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->delete($id);

            return $this->successResponse(message: 'User deleted successfully.');
        } catch (UserNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}

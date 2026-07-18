<?php

namespace App\Services;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\LoginUserData;
use App\DTOs\Auth\RegisterUserData;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;

class AuthService
{
    public function __construct(
        private RegisterUserAction $registerUserAction,
        private LoginUserAction $loginUserAction,
    ) {}

    /**
     * @return array{user: User, access_token: string, token_type: string, expires_in: int}
     * @throws UserAlreadyExistsException
     */
    public function register(RegisterUserData $data): array
    {
        $user = $this->registerUserAction->execute($data);
        $token = auth('api')->login($user);

        return $this->buildTokenPayload($user, $token);
    }

    /**
     * @return array{user: User, access_token: string, token_type: string, expires_in: int}|null
     */
    public function login(LoginUserData $data): ?array
    {
        $token = $this->loginUserAction->execute($data);

        if (! $token) {
            return null;
        }

        return $this->buildTokenPayload(auth('api')->user(), $token);
    }

    public function logout(): void
    {
        auth('api')->logout(forceForever: true);
    }

    /**
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    public function refresh(): array
    {
        $token = auth('api')->refresh();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    /**
     * @return array{user: User, access_token: string, token_type: string, expires_in: int}
     */
    private function buildTokenPayload(User $user, string $token): array
    {
        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}

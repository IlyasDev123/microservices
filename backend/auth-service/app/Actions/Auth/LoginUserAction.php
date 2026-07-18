<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginUserData;
use App\UserStatus;

class LoginUserAction
{
    public function execute(LoginUserData $data): ?string
    {
        $token = auth('api')->attempt([
            'email' => $data->email,
            'password' => $data->password,
            'status' => UserStatus::Active,
        ]);

        if (! $token) {
            return null;
        }

        auth('api')->user()->update(['last_login_at' => now()]);

        return $token;
    }
}

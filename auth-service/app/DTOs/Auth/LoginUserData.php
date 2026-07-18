<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\LoginRequest;

class LoginUserData
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->string('email')->lower()->value(),
            password: $request->string('password')->value(),
        );
    }
}

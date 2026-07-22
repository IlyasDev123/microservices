<?php

namespace App\DTOs\User;

use App\Http\Requests\User\CreateUserRequest;

class CreateUserData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(CreateUserRequest $request): self
    {
        return new self(
            id: $request->input('id'),
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
        );
    }
}

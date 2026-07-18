<?php

namespace App\DTOs\User;

use App\Http\Requests\User\UpdateProfileRequest;

class UpdateUserData
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $phone,
        public readonly ?string $bio,
    ) {}

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            name: $request->string('name')->trim()->value() ?: null,
            phone: $request->string('phone')->trim()->value() ?: null,
            bio: $request->string('bio')->trim()->value() ?: null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'phone' => $this->phone,
            'bio' => $this->bio,
        ], fn ($value) => ! is_null($value));
    }
}

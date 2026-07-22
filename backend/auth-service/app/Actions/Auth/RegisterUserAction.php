<?php

namespace App\Actions\Auth;

use App\Contracts\UserRepositoryInterface;
use App\DTOs\Auth\RegisterUserData;
use App\Events\UserCreated;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;
use App\UserStatus;

class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @throws UserAlreadyExistsException
     */
    public function execute(RegisterUserData $data): User
    {
        if ($this->userRepository->findByEmail($data->email)) {
            throw new UserAlreadyExistsException($data->email);
        }

        $user = $this->userRepository->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
            'status' => UserStatus::Active,
        ]);

        UserCreated::dispatch($user);

        return $user;
    }
}

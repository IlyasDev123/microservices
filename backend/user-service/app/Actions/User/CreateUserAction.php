<?php

namespace App\Actions\User;

use App\Contracts\UserRepositoryInterface;
use App\DTOs\User\CreateUserData;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;
use App\UserStatus;

class CreateUserAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepositoryInterface $userRepository,)
    {
        //
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function execute(CreateUserData $data): User
    {
        try {
            $user = $this->userRepository->upsert(
                ['id' => $data->id],
                [
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => $data->password,
                    'status' => UserStatus::Active,

                ]
            );
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

<?php

namespace App\Actions\User;

use App\Contracts\UserRepositoryInterface;
use App\DTOs\User\UpdateUserData;
use App\Exceptions\UserNotFoundException;
use App\Models\User;

class UpdateUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @throws UserNotFoundException
     */
    public function execute(int $id, UpdateUserData $data): User
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        $this->userRepository->update($user, $data->toArray());

        return $user->fresh();
    }
}

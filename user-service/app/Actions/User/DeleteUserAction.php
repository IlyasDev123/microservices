<?php

namespace App\Actions\User;

use App\Contracts\UserRepositoryInterface;
use App\Exceptions\UserNotFoundException;

class DeleteUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @throws UserNotFoundException
     */
    public function execute(int $id): void
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        $this->userRepository->delete($user);
    }
}

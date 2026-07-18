<?php

namespace App\Services;

use App\Actions\User\DeleteUserAction;
use App\Actions\User\GetUsersAction;
use App\Actions\User\UpdateUserAction;
use App\Contracts\UserRepositoryInterface;
use App\DTOs\User\UpdateUserData;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private GetUsersAction $getUsersAction,
        private UpdateUserAction $updateUserAction,
        private DeleteUserAction $deleteUserAction,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->getUsersAction->execute($perPage);
    }

    /**
     * @throws UserNotFoundException
     */
    public function show(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function update(int $id, UpdateUserData $data): User
    {
        return $this->updateUserAction->execute($id, $data);
    }

    /**
     * @throws UserNotFoundException
     */
    public function delete(int $id): void
    {
        $this->deleteUserAction->execute($id);
    }
}

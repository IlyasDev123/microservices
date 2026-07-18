<?php

namespace App\Actions\User;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUsersAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }
}

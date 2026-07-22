<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct()
    {
        //
    }
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function upsert(array $attributes, array $options): User
    {
        return User::updateOrCreate($attributes, $options);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::latest()->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function update(User $user, array $attributes): bool
    {
        return $user->update($attributes);
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}

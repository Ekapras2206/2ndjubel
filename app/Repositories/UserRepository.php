<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Update a user's profile.
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Toggle user's active status (e.g., for suspension).
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function toggleActiveStatus(User $user): bool
    {
        $user->is_active = !$user->is_active; // Asumsi ada kolom 'is_active' di tabel users
        return $user->save();
    }
}
<?php

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool { return $user->canPerm('users.view'); }
    public function create(User $user): bool  { return $user->canPerm('users.create'); }
    public function update(User $user, User $model): bool
    {
        return $user->canPerm('users.update') || $user->id === $model->id;
    }
    public function delete(User $user, User $model): bool
    {
        return $user->canPerm('users.delete') && $user->id !== $model->id;
    }
}
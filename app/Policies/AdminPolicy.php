<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function accessAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageUsers(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageKyc(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageSettings(User $user): bool
    {
        return $user->isAdmin();
    }

    public function impersonate(User $user): bool
    {
        return $user->isAdmin() && $user->email !== 'test@example.com';
    }
}

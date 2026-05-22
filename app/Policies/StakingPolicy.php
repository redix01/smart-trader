<?php

namespace App\Policies;

use App\Models\Stake;
use App\Models\User;

class StakingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Stake $stake): bool
    {
        return $user->id === $stake->user_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->kyc_level !== 'unverified';
    }

    public function managePlans(User $user): bool
    {
        return $user->isAdmin();
    }
}

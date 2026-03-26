<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only admins can create new admin-panel users.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}

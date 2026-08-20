<?php

namespace App\Modules\Auth\Actions;

use App\Modules\User\Models\User;

/**
 * Handle the user profile action.
 *
 * This action retrieves the authenticated user's profile
 * information for API responses.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ProfileAction
{
    /**
     * Execute the profile action.
     *
     * Returns the authenticated user's profile details.
     *
     * @param User $user The authenticated user.
     * @return User
     */
    public function execute(User $user): User
    {
        return $user;
    }
}
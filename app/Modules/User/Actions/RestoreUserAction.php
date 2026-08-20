<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;

/**
 * Handle the user restoration action.
 *
 * This action delegates the process of restoring
 * a soft-deleted user to the user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class RestoreUserAction
{
    /**
     * Create a new action instance.
     *
     * @param UserService $userService The user service.
     */
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Execute the user restoration action.
     *
     * Restores the specified soft-deleted user.
     *
     * @param string $uuid The user UUID.
     * @return User
     */
    public function execute(string $uuid): User
    {
        return $this->userService
            ->restore($uuid);
    }
}

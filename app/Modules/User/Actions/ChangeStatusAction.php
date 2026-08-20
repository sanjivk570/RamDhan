<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;

/**
 * Handle the user status change action.
 *
 * This action delegates the process of changing
 * a user's active status to the user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ChangeStatusAction
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
     * Execute the user status change action.
     *
     * Updates the active status of the specified user.
     *
     * @param string $uuid The user UUID.
     * @param bool $status The new user status.
     * @return User
     */
    public function execute(string $uuid, bool $status): User
    {

        return $this->userService
            ->changeStatus($uuid, $status);
    }
}
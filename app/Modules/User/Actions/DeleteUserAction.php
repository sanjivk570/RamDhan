<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Services\UserService;

/**
 * Handle the user deletion action.
 *
 * This action delegates the process of deleting
 * a user to the user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class DeleteUserAction
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
     * Execute the user deletion action.
     *
     * Deletes the specified user.
     *
     * @param string $uuid The user UUID.
     * @return void
     */
    public function execute(string $uuid): void
    {
        $this->userService->delete($uuid);
    }
}
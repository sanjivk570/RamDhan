<?php

declare(strict_types=1);

namespace App\Modules\User\Actions;

use App\Modules\User\Services\UserService;
use App\Modules\User\Models\User;

/**
 * Handle the user retrieval action.
 *
 * This action delegates the process of retrieving
 * the details of a specific user to the user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ShowUserAction
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
     * Execute the user retrieval action.
     *
     * Retrieves the details of the specified user.
     *
     * @param string $uuid The user UUID.
     * @return User
     */
    public function execute(string $uuid): User
    {
        return $this->userService->details($uuid);
    }
}
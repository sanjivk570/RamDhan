<?php

declare(strict_types=1);

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;

/**
 * Handle the user update action.
 *
 * This action delegates the process of updating
 * an existing user to the user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateUserAction
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
     * Execute the user update action.
     *
     * Updates the specified user using the validated data.
     *
     * @param string $uuid The user UUID.
     * @param array<string, mixed> $data The validated user data.
     * @return User
     */
    public function execute(string $uuid, array $data): User 
    {
        return $this->userService->update($uuid,$data);
    }
}
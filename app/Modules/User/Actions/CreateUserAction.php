<?php

declare(strict_types=1);

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;

/**
 * Handle the user creation action.
 *
 * This action delegates the process of creating
 * a new user to the user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class CreateUserAction
{
    /**
     * Create a new action instance.
     *
     * @param UserService $userService The user service.
     */
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    /**
     * Execute the user creation action.
     *
     * Creates a new user using the validated data.
     *
     * @param array<string, mixed> $data The validated user data.
     * @return User
     */
    public function execute(array $data): User
    {
        return $this->userService->create($data);
    }
}
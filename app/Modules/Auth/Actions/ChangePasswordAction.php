<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Models\User;

/**
 * Handle the change password action.
 *
 * This action delegates the password change process to the
 * authentication service.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ChangePasswordAction
{
    /**
     * Create a new action instance.
     *
     * @param AuthService $authService The authentication service.
     */
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * Execute the change password action.
     *
     * @param User $user The authenticated user.
     * @param array<string, mixed> $data The validated password data.
     * @return void
     */
    public function execute(User $user, array $data): void
    {
        $this->authService->changePassword($user, $data);
    }
}
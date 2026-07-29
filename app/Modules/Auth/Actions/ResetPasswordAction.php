<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\AuthService;

/**
 * Handle the password reset action.
 *
 * This action delegates the password reset process
 * to the authentication service.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ResetPasswordAction
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
     * Execute the password reset action.
     *
     * Resets the user's password using the validated
     * password reset data and token.
     *
     * @param array<string, mixed> $data The validated password reset data.
     * @return void
     */
    public function execute(array $data): void
    {
        $this->authService->resetPassword($data);
    }
}
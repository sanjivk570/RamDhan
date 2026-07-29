<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\AuthService;

/**
 * Handle the forgot password action.
 *
 * This action delegates the process of sending a password reset
 * link to the authentication service.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ForgotPasswordAction
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
     * Execute the forgot password action.
     *
     * Sends a password reset link to the specified email address.
     *
     * @param string $email The user's email address.
     * @return void
     */
    public function execute(string $email): void
    {
        $this->authService->forgotPassword($email);
    }
}
<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\AuthService;

/**
 * Handle the user login action.
 *
 * This action delegates the user authentication process
 * to the authentication service.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class LoginAction
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
     * Execute the login action.
     *
     * Authenticates the user using the provided credentials and
     * returns the authenticated user along with an access token.
     *
     * @param array<string, mixed> $credentials The validated login credentials.
     * @return array<string, mixed>
     */
    public function execute(array $credentials): array
    {
        return $this->authService->login($credentials);
    }
}
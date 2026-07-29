<?php

declare(strict_types=1);

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Models\User;

/**
 * Handle the user logout action.
 *
 * This action delegates the logout process to the
 * authentication service.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class LogoutAction
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
     * Execute the logout action.
     *
     * Revokes the current access token for the authenticated user.
     *
     * @param User $user The authenticated user.
     * @return void
     */
    public function execute(User $user): void
    {
        $this->authService->logout($user);
    }
}
<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\AuthService;

/**
 * Handle the user registration action.
 *
 * This action delegates the user registration process
 * to the authentication service.
 *
 * @package App\Modules\Auth\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class RegisterAction
{
    /**
     * Create a new action instance.
     *
     * @param AuthService $service The authentication service.
     */
    public function __construct(
        protected AuthService $service
    ) {}

    /**
     * Execute the registration action.
     *
     * Registers a new user and returns the created user
     * along with an authentication token.
     *
     * @param array<string, mixed> $data The validated registration data.
     * @return array<string, mixed>
     */
    public function execute(array $data)
    {
        return $this->service->register($data);
    }
}
<?php

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Handle validation rules for user login requests.
 *
 * This request validates the credentials required for user
 * authentication.
 *
 * @package App\Modules\Auth\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules for the login request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required','email'],
            'password' => ['required', 'string'],
        ];
    }
}
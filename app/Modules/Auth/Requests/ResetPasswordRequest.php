<?php

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Handle validation rules for password reset requests.
 *
 * This request validates the data required to reset a user's
 * password using a valid reset token.
 *
 * @package App\Modules\Auth\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class ResetPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        /**
         * Get the validation rules for the password reset request.
         *
         * @return array<string, mixed>
         */
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }
}
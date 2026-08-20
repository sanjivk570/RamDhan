<?php

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Handle validation rules for change password requests.
 *
 * This request validates the current password and the new password
 * when an authenticated user wants to update their password.
 *
 * @package App\Modules\Auth\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class ChangePasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules for the change password request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }
}
<?php

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Handle validation rules for user registration requests.
 *
 * This request validates the input data required to register
 * a new user in the application.
 *
 * @package App\Modules\Auth\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class RegisterRequest extends BaseRequest
{
    
    /**
     * Get the validation rules for the registration request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100',],

            'last_name' => ['nullable', 'string', 'max:100',],

            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email',],

            'mobile' => [ 'nullable', 'digits:10', 'unique:users,mobile'],

            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }
}
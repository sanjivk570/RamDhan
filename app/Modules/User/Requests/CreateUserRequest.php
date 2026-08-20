<?php

declare(strict_types=1);

namespace App\Modules\User\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Validate the request for creating a new user.
 *
 * Defines validation rules for user registration including
 * personal information, authentication credentials, role
 * assignment, and account status.
 *
 * @package App\Modules\User\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class CreateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * Validates user details before creating a new user record.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'country_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'mobile' => [
                'nullable',
                'string',
                'unique:users,mobile',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

            'role' => [
                'required',
                'exists:roles,name',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
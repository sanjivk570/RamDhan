<?php

declare(strict_types=1);

namespace App\Modules\User\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validate the request for updating an existing user.
 *
 * Defines validation rules for updating user information
 * including personal details, authentication credentials,
 * role assignment, and account status.
 *
 * @package App\Modules\User\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * Validates user update data while ignoring the
     * current user's unique fields during validation.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $uuid = $this->route('uuid');

        return [

            'first_name' => ['sometimes', 'string', 'max:100'],

            'last_name' => ['nullable', 'string', 'max:100'],

            'email' => [

                'sometimes',

                'email',

                Rule::unique('users', 'email')
                    ->ignore($uuid, 'uuid'),
            ],

            'mobile' => [

                'nullable',

                Rule::unique('users', 'mobile')
                    ->ignore($uuid, 'uuid'),
            ],

            'country_code' => [

                'nullable',

                'string',

                'max:10',
            ],

            'password' => [

                'nullable',

                'confirmed',

                Password::defaults(),
            ],

            'role' => [

                'sometimes',

                'exists:roles,name',
            ],

            'is_active' => [

                'sometimes',

                'boolean',
            ],

        ];
    }
}
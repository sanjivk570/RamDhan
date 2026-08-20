<?php

declare(strict_types=1);

namespace App\Modules\Role\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Handle validation for role creation requests.
 *
 * Defines the validation rules required to create
 * a new role.
 *
 * @package App\Modules\Role\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class CreateRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'unique:roles,name',
            ],

            'display_name' => [
                'required',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'guard_name' => [
                'sometimes',
                'string',
            ],

            'is_system' => [
                'sometimes',
                'boolean',
            ],

        ];
    }
}
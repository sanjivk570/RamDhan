<?php

declare(strict_types=1);

namespace App\Modules\Role\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Handle validation for role permission synchronization requests.
 *
 * Defines the validation rules for assigning or
 * synchronizing permissions with a role.
 *
 * @package App\Modules\Role\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class SyncRolePermissionRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [

            'permissions' => [
                'required',
                'array',
            ],

            'permissions.*' => [
                'string',
                'exists:permissions,name',
            ],

        ];
    }
}

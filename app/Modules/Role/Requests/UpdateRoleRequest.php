<?php

declare(strict_types=1);

namespace App\Modules\Role\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Handle validation for role update requests.
 *
 * Defines the validation rules for updating
 * an existing role.
 *
 * @package App\Modules\Role\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [

            'name' => [
                'sometimes',
                'string',
                Rule::unique('roles', 'name')->ignore($id),
            ],

            'display_name' => [
                'sometimes',
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
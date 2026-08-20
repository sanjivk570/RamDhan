<?php

namespace App\Modules\User\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Validate the request for changing user status.
 *
 * Ensures that the status value provided for a user
 * is present and is a valid boolean value.
 *
 * @package App\Modules\User\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class ChangeStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [

            'status' => [
                'required',
                'boolean'
            ]

        ];
    }
}
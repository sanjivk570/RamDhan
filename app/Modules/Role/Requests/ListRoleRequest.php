<?php

declare(strict_types=1);

namespace App\Modules\Role\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Handle validation for role listing requests.
 *
 * Defines the validation rules for filtering,
 * sorting, and paginating role records.
 *
 * @package App\Modules\Role\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class ListRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [

            'search' => ['nullable', 'string'],

            'per_page' => [
                'nullable',
                'integer',
                'min:10',
                'max:100',
            ],

            'sort_by' => [
                'nullable',
                'in:name,created_at',
            ],

            'sort_order' => [
                'nullable',
                'in:asc,desc',
            ],

        ];
    }
}
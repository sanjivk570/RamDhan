<?php

declare(strict_types=1);

namespace App\Modules\User\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Support\Facades\Schema;

/**
 * Validate the request for listing users.
 *
 * Defines validation rules for user listing filters
 * including search, status filtering, pagination,
 * and sorting options.
 *
 * @package App\Modules\User\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class UserListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the validation rules for the request.
     *
     * Validates optional filters used for retrieving
     * a paginated list of users.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            //'status' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'sort_by' => [
                'nullable',
                //'in:first_name,email,created_at',
                'in:' . implode(',', self::SORTABLE_COLUMNS),
            ],
            'sort_order' => [
                'nullable',
                'in:asc,desc'
            ],

            'filters' => [
                'nullable',
                'array',
            ],

            'filters.first_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.last_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.email' => [
                'nullable',
                'string',
                'max:255',
            ],

            'filters.mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'filters.status' => [
                'nullable',
                'boolean',
            ],

        ];
    }
}
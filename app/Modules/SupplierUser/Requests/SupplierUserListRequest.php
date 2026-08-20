<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Requests;

use App\Core\Requests\BaseRequest;

final class SupplierUserListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'first_name', 'last_name', 'email', 'mobile', 'created_at', 'updated_at',
    ];

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'sort_by' => ['nullable', 'in:' . implode(',', self::SORTABLE_COLUMNS)],
            'sort_order' => ['nullable', 'in:asc,desc'],
            'filters' => ['nullable', 'array'],
            'filters.status' => ['nullable', 'boolean'],
            'filters.primary' => ['nullable', 'boolean'],
            'filters.role' => ['nullable', 'string', 'max:100'],
        ];
    }
}

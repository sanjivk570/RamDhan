<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing coupons.
 *
 * @package App\Modules\Promotion\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class CouponListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'used_count',
        'starts_at',
        'ends_at',
        'created_at',
    ];

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'sort_by' => ['nullable', Rule::in(self::SORTABLE_COLUMNS)],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],

            'filters' => ['nullable', 'array'],

            'filters.discount_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'filters.is_active' => ['nullable', 'boolean'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
            'filters.created_from' => ['nullable', 'date'],
            'filters.created_to' => ['nullable', 'date', 'after_or_equal:filters.created_from'],
        ];
    }
}
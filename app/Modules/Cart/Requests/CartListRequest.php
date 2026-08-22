<?php

declare(strict_types=1);

namespace App\Modules\Cart\Requests;

use App\Core\Requests\BaseRequest;
use App\Modules\Cart\Models\Cart;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing carts.
 *
 * Defines validation rules for cart listing filters including
 * search, customer, status, coupon code, currency, date range,
 * cart totals, pagination, and sorting options.
 *
 * @package App\Modules\Cart\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class CartListRequest extends BaseRequest
{
    /**
     * Columns that are allowed to be used for sorting.
     *
     * @var array<int, string>
     */
    private const SORTABLE_COLUMNS = [
        'status',
        'coupon_code',
        'grand_total',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'sort_by' => ['nullable', Rule::in(self::SORTABLE_COLUMNS)],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],

            'filters' => ['nullable', 'array'],

            'filters.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'filters.status' => ['nullable', Rule::in([Cart::ACTIVE, Cart::CONVERTED, Cart::MERGED])],
            'filters.coupon_code' => ['nullable', 'string', 'max:100'],
            'filters.currency_code' => ['nullable', 'string', 'max:3'],
            'filters.min_total' => ['nullable', 'numeric', 'min:0'],
            'filters.max_total' => ['nullable', 'numeric', 'gte:filters.min_total'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
        ];
    }
}
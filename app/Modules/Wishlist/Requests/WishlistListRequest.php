<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing wishlist records.
 *
 * Defines validation rules for wishlist listing filters including
 * search, customer, product, date range, pagination, and sorting.
 *
 * @package App\Modules\Wishlist\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class WishlistListRequest extends BaseRequest
{
    /**
     * Columns that are allowed to be used for sorting.
     *
     * @var array<int, string>
     */
    private const SORTABLE_COLUMNS = [
        'customer_id',
        'product_id',
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
            'filters.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'filters.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
        ];
    }
}
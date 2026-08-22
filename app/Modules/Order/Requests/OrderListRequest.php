<?php

declare(strict_types=1);

namespace App\Modules\Order\Requests;

use App\Core\Requests\BaseRequest;
use App\Modules\Order\Models\Order;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing orders.
 *
 * @package App\Modules\Order\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class OrderListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'order_number',
        'status',
        'payment_status',
        'fulfillment_status',
        'grand_total',
        'placed_at',
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

            'filters.status' => ['nullable', Rule::in([
                Order::PENDING,
                Order::CONFIRMED,
                Order::PROCESSING,
                Order::SHIPPED,
                Order::DELIVERED,
                Order::CANCELLED,
                Order::COMPLETED,
            ])],
            'filters.payment_status' => ['nullable', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            'filters.fulfillment_status' => ['nullable', Rule::in(['unfulfilled', 'partially_fulfilled', 'fulfilled', 'cancelled'])],
            'filters.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'filters.payment_method' => ['nullable', 'string', 'max:40'],
            'filters.coupon_code' => ['nullable', 'string', 'max:100'],
            'filters.min_total' => ['nullable', 'numeric', 'min:0'],
            'filters.max_total' => ['nullable', 'numeric', 'gte:filters.min_total'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
        ];
    }
}

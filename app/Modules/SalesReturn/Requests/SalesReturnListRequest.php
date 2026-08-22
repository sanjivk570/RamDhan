<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing sales returns.
 *
 * @package App\Modules\SalesReturn\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class SalesReturnListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'return_number',
        'status',
        'refund_status',
        'total_amount',
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

            'filters.status' => ['nullable', Rule::in(['requested', 'approved', 'rejected', 'received', 'partially_received'])],
            'filters.refund_status' => ['nullable', Rule::in(['pending', 'processed', 'failed'])],
            'filters.order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'filters.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'filters.min_total' => ['nullable', 'numeric', 'min:0'],
            'filters.max_total' => ['nullable', 'numeric', 'gte:filters.min_total'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
        ];
    }
}
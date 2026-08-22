<?php

declare(strict_types=1);

namespace App\Modules\Payment\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing payment transactions.
 *
 * @package App\Modules\Payment\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class PaymentTransactionListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'amount',
        'status',
        'created_at',
        'updated_at',
    ];

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'sort_by' => ['nullable', Rule::in(self::SORTABLE_COLUMNS)],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],

            'filters' => ['nullable', 'array'],

            'filters.status' => ['nullable', Rule::in(['pending', 'processing', 'success', 'failed', 'cancelled', 'refunded'])],
            'filters.provider' => ['nullable', 'string', 'max:50'],
            'filters.payment_method' => ['nullable', 'string', 'max:50'],
            'filters.transaction_type' => ['nullable', Rule::in(['payment', 'refund', 'chargeback'])],
            'filters.order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'filters.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'filters.min_amount' => ['nullable', 'numeric', 'min:0'],
            'filters.max_amount' => ['nullable', 'numeric', 'gte:filters.min_amount'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
        ];
    }
}
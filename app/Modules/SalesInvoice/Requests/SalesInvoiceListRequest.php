<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing sales invoices.
 *
 * @package App\Modules\SalesInvoice\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class SalesInvoiceListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'invoice_number',
        'status',
        'invoice_date',
        'due_date',
        'grand_total',
        'paid_amount',
        'due_amount',
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

            'filters.status' => ['nullable', Rule::in(['issued', 'partial', 'paid', 'void', 'cancelled'])],
            'filters.order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'filters.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'filters.currency_code' => ['nullable', 'string', 'max:3'],
            'filters.min_total' => ['nullable', 'numeric', 'min:0'],
            'filters.max_total' => ['nullable', 'numeric', 'gte:filters.min_total'],
            'filters.due_only' => ['nullable', 'boolean'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
            'filters.due_from' => ['nullable', 'date'],
            'filters.due_to' => ['nullable', 'date', 'after_or_equal:filters.due_from'],
        ];
    }
}
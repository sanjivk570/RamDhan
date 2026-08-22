<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for listing shipments.
 *
 * @package App\Modules\Shipment\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class ShipmentListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'shipment_number',
        'status',
        'carrier',
        'tracking_number',
        'shipped_at',
        'delivered_at',
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

            'filters.status' => ['nullable', Rule::in(['pending', 'created', 'shipped', 'delivered', 'cancelled'])],
            'filters.carrier' => ['nullable', 'string', 'max:100'],
            'filters.service' => ['nullable', 'string', 'max:100'],
            'filters.order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'filters.customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'filters.from_date' => ['nullable', 'date'],
            'filters.to_date' => ['nullable', 'date', 'after_or_equal:filters.from_date'],
            'filters.shipped_from' => ['nullable', 'date'],
            'filters.shipped_to' => ['nullable', 'date', 'after_or_equal:filters.shipped_from'],
        ];
    }
}
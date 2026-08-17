<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Modules\Purchase\Models\PurchaseOrder;

final class PurchaseOrderListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable','string','max:100'],
            'per_page' => ['nullable','integer','min:10','max:100'],
            'sort_by' => ['nullable', Rule::in(['po_number','order_date','expected_date','grand_total','status','created_at'])],
            'sort_order' => ['nullable', Rule::in(['asc','desc'])],
            'filters' => ['nullable','array'],
            'filters.supplier' => ['nullable','integer','exists:suppliers,id'],
            'filters.status' => ['nullable', Rule::in([PurchaseOrder::DRAFT,PurchaseOrder::SUBMITTED,PurchaseOrder::APPROVED,PurchaseOrder::PARTIALLY_RECEIVED,PurchaseOrder::RECEIVED,PurchaseOrder::CANCELLED,PurchaseOrder::CLOSED])],
            'filters.from_date' => ['nullable','date'],
            'filters.to_date' => ['nullable','date','after_or_equal:filters.from_date'],
        ];
    }
}

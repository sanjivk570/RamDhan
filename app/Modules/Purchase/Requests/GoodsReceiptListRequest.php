<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Modules\Purchase\Models\GoodsReceipt;

final class GoodsReceiptListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable','string','max:100'],
            'per_page' => ['nullable','integer','min:10','max:100'],
            'sort_by' => ['nullable', Rule::in(['grn_number','receipt_date','status','created_at'])],
            'sort_order' => ['nullable', Rule::in(['asc','desc'])],
            'filters' => ['nullable','array'],
            'filters.supplier' => ['nullable','integer','exists:suppliers,id'],
            'filters.status' => ['nullable', Rule::in([GoodsReceipt::DRAFT,GoodsReceipt::POSTED,GoodsReceipt::VOID])],
            'filters.from_date' => ['nullable','date'],
            'filters.to_date' => ['nullable','date','after_or_equal:filters.from_date'],
        ];
    }
}

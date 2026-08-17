<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;

final class CreateGoodsReceiptRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'purchase_order_uuid' => ['required','uuid','exists:purchase_orders,uuid'],
            'receipt_date' => ['required','date'],
            'supplier_document_date' => ['nullable','date'],
            'supplier_document_number' => ['nullable','string','max:100'],
            'notes' => ['nullable','string','max:5000'],
            'items' => ['required','array','min:1','max:500'],
            'items.*.purchase_order_item_uuid' => ['required','uuid','exists:purchase_order_items,uuid'],
            'items.*.received_quantity' => ['required','numeric','gt:0'],
            'items.*.accepted_quantity' => ['required','numeric','gte:0'],
            'items.*.rejected_quantity' => ['nullable','numeric','gte:0'],
            'items.*.unit_cost' => ['nullable','numeric','min:0'],
            'items.*.batch_number' => ['nullable','string','max:100'],
            'items.*.expiry_date' => ['nullable','date'],
            'items.*.notes' => ['nullable','string','max:2000'],
        ];
    }
}

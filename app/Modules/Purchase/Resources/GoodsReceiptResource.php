<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class GoodsReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'grn_number' => $this->grn_number,
            'status' => $this->status,
            'supplier' => $this->supplier ? [
                'uuid' => $this->supplier->uuid,
                'code' => $this->supplier->supplier_code,
                'company_name' => $this->supplier->company_name,
            ] : null,
            'purchase_order' => $this->purchaseOrder ? [
                'uuid' => $this->purchaseOrder->uuid,
                'po_number' => $this->purchaseOrder->po_number,
            ] : null,
            'receipt_date' => $this->receipt_date?->toDateString(),
            'supplier_document_date' => $this->supplier_document_date?->toDateString(),
            'supplier_document_number' => $this->supplier_document_number,
            'notes' => $this->notes,
            'posted_at' => $this->posted_at,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'uuid' => $item->uuid,
                'purchase_order_item_uuid' => $item->purchaseOrderItem?->uuid,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'ordered_quantity' => $item->ordered_quantity,
                'previously_received_quantity' => $item->previously_received_quantity,
                'received_quantity' => $item->received_quantity,
                'accepted_quantity' => $item->accepted_quantity,
                'rejected_quantity' => $item->rejected_quantity,
                'unit_cost' => $item->unit_cost,
                'batch_number' => $item->batch_number,
                'expiry_date' => $item->expiry_date?->toDateString(),
                'notes' => $item->notes,
            ])->values()),
            'created_at' => $this->created_at,
        ];
    }
}

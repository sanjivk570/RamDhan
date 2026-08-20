<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'po_number' => $this->po_number,
            'supplier' => $this->supplier ? [
                'uuid' => $this->supplier->uuid,
                'code' => $this->supplier->supplier_code,
                'company_name' => $this->supplier->company_name,
            ] : null,
            'status' => $this->status,
            'order_date' => $this->order_date?->toDateString(),
            'expected_date' => $this->expected_date?->toDateString(),
            'payment_terms_days' => $this->payment_terms_days,
            'currency_code' => $this->currency_code,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'grand_total' => $this->grand_total,
            'notes' => $this->notes,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'uuid' => $item->uuid,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'unit_id' => $item->unit_id,
                'sku' => $item->sku,
                'description' => $item->description,
                'ordered_quantity' => $item->ordered_quantity,
                'received_quantity' => $item->received_quantity,
                'remaining_quantity' => $item->remainingQuantity(),
                'unit_price' => $item->unit_price,
                'discount_amount' => $item->discount_amount,
                'tax_rate' => $item->tax_rate,
                'tax_amount' => $item->tax_amount,
                'line_subtotal' => $item->line_subtotal,
                'line_total' => $item->line_total,
            ])->values()),
            'created_at' => $this->created_at,
        ];
    }
}

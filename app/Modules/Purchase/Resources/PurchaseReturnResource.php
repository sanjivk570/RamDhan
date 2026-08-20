<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'return_number' => $this->return_number,
            'supplier_id' => $this->supplier_id,
            'goods_receipt_id' => $this->goods_receipt_id,
            'status' => $this->status,
            'return_date' => $this->return_date,
            'total_amount' => $this->total_amount,
            'currency_code' => $this->currency_code,
            'reason' => $this->reason,
            'posted_at' => $this->posted_at,
            'items' => $this->whenLoaded('items', function () {
                return collect($this->items)->map(fn ($item) => [
                    'uuid' => $item->uuid,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'line_total' => $item->line_total,
                    'reason' => $item->reason,
                ])->values();
            }),
        ];
    }
}

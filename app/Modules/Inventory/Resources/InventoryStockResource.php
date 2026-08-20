<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryStockResource extends JsonResource
{
    /**
     * Transform resource into array.
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "product" => $this->whenLoaded("product", function () {
                return [
                    "uuid" => $this->product->uuid,
                    "name" => $this->product->name,
                    "sku" => $this->product->sku,
                ];
            }),

            "quantity" => (float) $this->quantity,

            "reserved_quantity" => (float) $this->reserved_quantity,

            "available_quantity" => (float) $this->available_quantity,

            "low_stock_threshold" =>
                $this->low_stock_threshold !== null
                    ? (float) $this->low_stock_threshold
                    : null,

            "is_low_stock" => $this->isLowStock(),

            "is_active" => (bool) $this->is_active,

            "created_at" => $this->created_at?->toISOString(),

            "updated_at" => $this->updated_at?->toISOString(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Inventory\Resources\InventoryStockResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "product" => [
                "uuid" => $this->product?->uuid,
                "name" => $this->product?->name,
            ],

            "name" => $this->name,
            "sku" => $this->sku,

            "price" => $this->price,
            "compare_price" => $this->compare_price,
            "cost_price" => $this->cost_price,

            "is_default" => $this->is_default,
            "is_active" => $this->is_active,
            "sort_order" => $this->sort_order,

            "attribute_values" => ProductVariantAttributeValueResource::collection(
                $this->whenLoaded("attributeValues")
            ),

            'inventory' => new InventoryStockResource(
                $this->whenLoaded('inventoryStock')
            ),

            "created_at" => $this->created_at?->toISOString(),

            "updated_at" => $this->updated_at?->toISOString(),
        ];
    }
}

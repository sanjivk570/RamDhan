<?php

declare(strict_types=1);

namespace App\Modules\Product\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Media\Resources\MediaResource;

use App\Modules\Unit\Resources\UnitResource;
use App\Modules\Tax\Resources\TaxClassResource;
use App\Modules\Inventory\Resources\InventoryStockResource;
use App\Modules\ProductVariant\Resources\ProductVariantResource;

/**
 * Product API resource.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ProductResource extends JsonResource
{
    
    /**
     * Transform the product resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            "unit_id" => $this->unit_id,
            "tax_class_id" => $this->tax_class_id,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'cost_price' => $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'categories' =>
                $this->whenLoaded(
                    'categories'
                ),
            // 'images' => ProductImageResource::collection(
            //     $this->whenLoaded('images')
            // ),

            /*
             * Product media.
             *
             * Only loaded when explicitly eager loaded.
             */
            'images' => MediaResource::collection(
                $this->whenLoaded('images')
            ),

            'unit' => new UnitResource(
                $this->whenLoaded('unit')
            ),

            'tax_class' => new TaxClassResource(
                $this->whenLoaded('taxClass')
            ),

            'inventory' => new InventoryStockResource(
                $this->whenLoaded('inventoryStock')
            ),

            'variants' => ProductVariantResource::collection(
                $this->whenLoaded('variants')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

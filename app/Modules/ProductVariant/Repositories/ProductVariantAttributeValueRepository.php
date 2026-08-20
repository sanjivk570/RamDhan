<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Repositories;

use App\Modules\ProductVariant\Models\ProductVariantAttributeValue;

class ProductVariantAttributeValueRepository
{
    public function sync(int $variantId, array $attributeValueIds): void
    {
        ProductVariantAttributeValue::query()
            ->where("product_variant_id", $variantId)
            ->delete();

        $rows = [];

        foreach (array_unique($attributeValueIds) as $attributeValueId) {
            $rows[] = [
                "product_variant_id" => $variantId,
                "attribute_value_id" => $attributeValueId,
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }

        if (!empty($rows)) {
            ProductVariantAttributeValue::insert($rows);
        }
    }

    public function deleteByVariant(int $variantId): int
    {
        return ProductVariantAttributeValue::query()
            ->where("product_variant_id", $variantId)
            ->delete();
    }
}

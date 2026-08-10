<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Repositories;

use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductVariantRepository
{
    public function paginate(
        string $productUuid,
        array $filters
    ): LengthAwarePaginator {
        $product = Product::where("uuid", $productUuid)->firstOrFail();

        return ProductVariant::query()
            ->with(["attributeValues.attributeValue.attribute"])
            ->where("product_id", $product->id)

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("name", "LIKE", "%{$search}%")->orWhere(
                        "sku",
                        "LIKE",
                        "%{$search}%"
                    );
                });
            })

            ->when(isset($filters["is_active"]), function ($query) use (
                $filters
            ) {
                $query->where("is_active", (bool) $filters["is_active"]);
            })

            ->orderBy(
                $filters["sort_by"] ?? "sort_order",
                $filters["sort_order"] ?? "asc"
            )

            ->paginate($filters["per_page"] ?? 20);
    }

    public function findByUuidAndProduct(
        string $uuid,
        int $productId
    ): ?ProductVariant {
        return ProductVariant::query()
            ->with(["attributeValues.attributeValue.attribute", "product"])
            ->where("uuid", $uuid)
            ->where("product_id", $productId)
            ->first();
    }

    public function findByUuidAndProductOrFail(
        string $uuid,
        int $productId
    ): ProductVariant {
        return ProductVariant::query()
            ->with(["attributeValues.attributeValue.attribute", "product"])
            ->where("uuid", $uuid)
            ->where("product_id", $productId)
            ->firstOrFail();
    }

    public function create(array $data): ProductVariant
    {
        return ProductVariant::create($data);
    }

    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update($data);

        return $variant->refresh();
    }

    public function delete(ProductVariant $variant): bool
    {
        return (bool) $variant->delete();
    }

    public function forceDelete(ProductVariant $variant): bool
    {
        return (bool) $variant->forceDelete();
    }

    public function clearDefault(int $productId): void
    {
        ProductVariant::query()
            ->where("product_id", $productId)
            ->update([
                "is_default" => false,
            ]);
    }

    public function setDefault(ProductVariant $variant): ProductVariant
    {
        $variant->update([
            "is_default" => true,
        ]);

        return $variant->refresh();
    }
}

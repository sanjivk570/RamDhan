<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Repositories;

use App\Modules\Inventory\Models\InventoryStock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;

class InventoryStockRepository
{

    /** 
     * Create initial inventory stock for a product.
     *
     * @param Product $product
     * @return InventoryStock
     */
    public function createInitialStockForProduct(Product $product): InventoryStock
    {
        return InventoryStock::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'product_id' => $product->id,
            'quantity' => 0,
            'reserved_quantity' => 0,
            'low_stock_threshold' => $product->low_stock_threshold,
            'is_active' => true,
        ]);
    }

    /** 
     * Create initial inventory stock for a product.
     *
     * @param Product $product
     * @return InventoryStock
     */
    public function createInitialStockForProductVariant(ProductVariant $productVariant): InventoryStock
    {
        return InventoryStock::create([
            //'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'product_id' => $productVariant->product_id,
            'product_variant_id' => $productVariant->id,
            'quantity' => 0,
            'reserved_quantity' => 0,
            'low_stock_threshold' => $productVariant->low_stock_threshold,
            'is_active' => true,
        ]);
    }

    /**
     * Get paginated inventory records.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return InventoryStock::query()
            ->with(["product"])

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->whereHas("product", function ($productQuery) use (
                    $search
                ) {
                    $productQuery
                        ->where("name", "LIKE", "%{$search}%")
                        ->orWhere("sku", "LIKE", "%{$search}%");
                });
            })

            ->when(isset($filters["is_active"]), function ($query) use (
                $filters
            ) {
                $query->where("is_active", (bool) $filters["is_active"]);
            })

            ->orderBy(
                $filters["sort_by"] ?? "created_at",
                $filters["sort_order"] ?? "desc"
            )

            ->paginate((int) ($filters["per_page"] ?? 20));
    }

    /**
     * Find inventory by UUID.
     */
    public function findByUuid(string $uuid): ?InventoryStock
    {
        return InventoryStock::with(["product"])
            ->where("uuid", $uuid)
            ->first();
    }

    /**
     * Find inventory by UUID or fail.
     */
    public function findByUuidOrFail(string $uuid): InventoryStock
    {
        return InventoryStock::with(["product"])
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    /**
     * Find inventory by product ID.
     */
    public function findByProductId(int $productId): ?InventoryStock
    {
        return InventoryStock::where("product_id", $productId)->first();
    }

    /**
     * Find inventory by product ID or create it.
     */
    public function firstOrCreateForProduct(
        int $productId,
        ?float $lowStockThreshold = null
    ): InventoryStock {
        return InventoryStock::firstOrCreate(
            [
                "product_id" => $productId,
            ],
            [
                "uuid" => (string) \Illuminate\Support\Str::uuid(),
                "quantity" => 0,
                "reserved_quantity" => 0,
                "low_stock_threshold" => $lowStockThreshold,
                "is_active" => true,
            ]
        );
    }

    /**
     * Create inventory stock.
     */
    public function create(array $data): InventoryStock
    {
        return InventoryStock::create($data);
    }

    /**
     * Update inventory stock.
     */
    public function update(
        InventoryStock $inventoryStock,
        array $data
    ): InventoryStock {
        $inventoryStock->update($data);

        return $inventoryStock->refresh();
    }

    /**
     * Update current quantity.
     */
    public function updateQuantity(
        InventoryStock $inventoryStock,
        float $quantity
    ): InventoryStock {
        $inventoryStock->update([
            "quantity" => $quantity,
        ]);

        return $inventoryStock->refresh();
    }

    /**
     * Get inventory transaction history.
     */
    public function transactions(
        InventoryStock $inventoryStock,
        int $perPage = 20
    ): LengthAwarePaginator {
        return $inventoryStock
            ->transactions()
            ->with(["createdBy"])
            ->latest()
            ->paginate($perPage);
    }
}

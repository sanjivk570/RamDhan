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
    public function createInitialStockForProduct(Product $product, array $data): InventoryStock
    {
        return InventoryStock::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'product_id' => $product->id,
            'quantity' => $data['stock_quantity'] ? $data['stock_quantity'] : 0,
            'reserved_quantity' => 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ? $data['low_stock_threshold'] : 0,
            'is_active' => true,
        ]);
    }

    /** 
     * Create initial inventory stock for a product.
     *
     * @param Product $product
     * @return InventoryStock
     */
    public function createInitialStockForProductVariant(ProductVariant $productVariant, array $data): InventoryStock
    {
        return InventoryStock::create([
            //'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'product_id' => $productVariant->product_id,
            'product_variant_id' => $productVariant->id,
            'quantity' => $data['stock_quantity'] ? $data['stock_quantity'] : 0,
            'reserved_quantity' => 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ? $data['low_stock_threshold'] : 0,
            'is_active' => true,
        ]);
    }


    /** 
     * upd initial inventory stock for a product.
     *
     * @param Product $product
     * @return InventoryStock
     */
    public function updateInitialStockForProduct(Product $product, array $data): InventoryStock
    {
        $stock = InventoryStock::where('product_id', $product->id)->first();

        if(!empty($data['stock_quantity']) ){
            $stock->quantity = $data['stock_quantity'];
        }
        if(!empty($data['stock_quantity']) ){
            $stock->low_stock_threshold = $data['low_stock_threshold'];
        }
        $stock->save();
        return $stock->fresh();
    }

    /** 
     * Update initial inventory stock for a product variant.
     *
     * @param ProductVariant $productVariant
     * @return InventoryStock
     */
    public function updateInitialStockForProductVariant(ProductVariant $productVariant, array $data): InventoryStock
    {
        $stock = InventoryStock::where('product_variant_id', $productVariant->id)->where('product_id', $productVariant->product_id)->first();

        if(!empty($data['stock_quantity']) ){
            $stock->quantity = $data['stock_quantity'];
        }
        if(!empty($data['stock_quantity']) ){
            $stock->low_stock_threshold = $data['low_stock_threshold'];
        }
        $stock->save();

        return $stock->fresh();
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

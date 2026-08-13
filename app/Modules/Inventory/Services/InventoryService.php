<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Models\InventoryStock;
use App\Modules\Inventory\Models\InventoryTransaction;
use App\Modules\Inventory\Repositories\InventoryStockRepository;
use App\Modules\Inventory\Repositories\InventoryTransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;

class InventoryService
{
    public function __construct(
        private readonly InventoryStockRepository $inventoryStockRepository,
        private readonly InventoryTransactionRepository $inventoryTransactionRepository
    ) {}

    /**
     * Create initial inventory stock for a product.
     *
     * New products always start with zero stock.
     *
     * @param Product $product
     * @return InventoryStock
     */
    public function createInitialStockForProduct(Product $product, array $data): InventoryStock {
        return $this->inventoryStockRepository->createInitialStockForProduct($product, $data);
    }

    /**
     * Create initial inventory stock for a product.
     *
     * New products always start with zero stock.
     *
     * @param ProductVariant $productVariant
     * @return InventoryStock
     */
    public function createInitialStockForProductVariant(ProductVariant $productVariant, array $data): InventoryStock {
        return $this->inventoryStockRepository->createInitialStockForProductVariant($productVariant, $data);
    }

    /**
     * Update initial inventory stock for a product.
     *
     * New products always start with zero stock.
     *
     * @param Product $product
     * @return InventoryStock
     */
    public function updateInitialStockForProduct(Product $product, array $data): InventoryStock {
        return $this->inventoryStockRepository->updateInitialStockForProduct($product, $data);
    }

    /**
     * Create initial inventory stock for a product variant
     *
     * New products always start with zero stock.
     *
     * @param ProductVariant $productVariant
     * @return InventoryStock
     */
    public function updateInitialStockForProductVariant(ProductVariant $productVariant, array $data): InventoryStock {
        return $this->inventoryStockRepository->updateInitialStockForProductVariant($productVariant, $data);
    }

    /**
     * Get paginated inventory.
     */
    public function list(array $filters)
    {
        return $this->inventoryStockRepository->paginate($filters);
    }

    /**
     * Get inventory details.
     */
    public function details(string $uuid): InventoryStock
    {
        return $this->inventoryStockRepository->findByUuidOrFail($uuid);
    }

    /**
     * Increase inventory stock.
     *
     * Example:
     * Purchase +50
     * Return +2
     */
    public function stockIn(
        string $uuid,
        float $quantity,
        string $type = InventoryTransaction::TYPE_PURCHASE,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $notes = null,
        ?int $createdBy = null
    ): InventoryStock {
        if ($quantity <= 0) {
            throw new RuntimeException(
                "Stock quantity must be greater than zero."
            );
        }

        return DB::transaction(function () use (
            $uuid,
            $quantity,
            $type,
            $referenceType,
            $referenceId,
            $notes,
            $createdBy
        ) {
            $stock = InventoryStock::query()
                ->where("uuid", $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) $stock->quantity;

            $after = $before + $quantity;

            $stock->update([
                "quantity" => $after,
            ]);

            $this->inventoryTransactionRepository->create([
                "uuid" => (string) Str::uuid(),
                "inventory_stock_id" => $stock->id,
                "product_id" => $stock->product_id,
                "product_variant_id" => $stock->product_variant_id,
                "type" => $type,
                "quantity" => $quantity,
                "quantity_before" => $before,
                "quantity_after" => $after,
                "reference_type" => $referenceType,
                "reference_id" => $referenceId,
                "notes" => $notes,
                "created_by" => $createdBy,
            ]);

            return $stock->refresh();
        });
    }

    /**
     * Decrease inventory stock.
     *
     * Example:
     * Sale -5
     * Damage -2
     */
    public function stockOut(
        string $uuid,
        float $quantity,
        string $type = InventoryTransaction::TYPE_SALE,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $notes = null,
        ?int $createdBy = null
    ): InventoryStock {
        if ($quantity <= 0) {
            throw new RuntimeException(
                "Stock quantity must be greater than zero."
            );
        }

        return DB::transaction(function () use (
            $uuid,
            $quantity,
            $type,
            $referenceType,
            $referenceId,
            $notes,
            $createdBy
        ) {
            $stock = InventoryStock::query()
                ->where("uuid", $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) $stock->quantity;

            $available = $before - (float) $stock->reserved_quantity;

            if ($quantity > $available) {
                throw new RuntimeException("Insufficient available stock.");
            }

            $after = $before - $quantity;

            $stock->update([
                "quantity" => $after,
            ]);

            $this->inventoryTransactionRepository->create([
                "uuid" => (string) Str::uuid(),
                "inventory_stock_id" => $stock->id,
                "product_id" => $stock->product_id,
                "product_variant_id" => $stock->product_variant_id,
                "type" => $type,
                "quantity" => $quantity,
                "quantity_before" => $before,
                "quantity_after" => $after,
                "reference_type" => $referenceType,
                "reference_id" => $referenceId,
                "notes" => $notes,
                "created_by" => $createdBy,
            ]);

            return $stock->refresh();
        });
    }

    /**
     * Adjust inventory to an exact quantity.
     *
     * Example:
     *
     * Existing = 50
     * Physical count = 47
     *
     * Adjustment = -3
     */
    public function adjust(
        string $uuid,
        float $newQuantity,
        ?string $notes = null,
        ?int $createdBy = null
    ): InventoryStock {
        if ($newQuantity < 0) {
            throw new RuntimeException("Stock quantity cannot be negative.");
        }

        return DB::transaction(function () use (
            $uuid,
            $newQuantity,
            $notes,
            $createdBy
        ) {
            $stock = InventoryStock::query()
                ->where("uuid", $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) $stock->quantity;

            if ($newQuantity < (float) $stock->reserved_quantity) {
                throw new RuntimeException(
                    "Adjusted quantity cannot be less than reserved quantity."
                );
            }

            $difference = $newQuantity - $before;

            if ($difference == 0.0) {
                return $stock->refresh();
            }

            $stock->update([
                "quantity" => $newQuantity,
            ]);

            $this->inventoryTransactionRepository->create([
                "uuid" => (string) Str::uuid(),
                "inventory_stock_id" => $stock->id,
                "product_id" => $stock->product_id,
                "product_variant_id" => $stock->product_variant_id,
                "type" => InventoryTransaction::TYPE_ADJUSTMENT,
                "quantity" => abs($difference),
                "quantity_before" => $before,
                "quantity_after" => $newQuantity,
                "notes" => $notes,
                "created_by" => $createdBy,
            ]);

            return $stock->refresh();
        });
    }

    /**
     * Get transaction history.
     */
    public function transactions(string $uuid, int $perPage = 20)
    {
        $stock = $this->inventoryStockRepository->findByUuidOrFail($uuid);

        return $this->inventoryTransactionRepository->paginate(
            $stock,
            $perPage
        );
    }
}

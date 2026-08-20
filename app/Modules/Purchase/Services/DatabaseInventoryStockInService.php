<?php

// declare(strict_types=1);

// namespace App\Modules\Purchase\Services;

// use App\Modules\Purchase\Contracts\InventoryStockInContract;
// use App\Modules\Purchase\Models\GoodsReceipt;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Schema;
// use RuntimeException;

// /**
//  * Adapter for the existing inventory_stocks/inventory_transactions tables.
//  * It intentionally uses schema introspection because the inventory module was
//  * created separately and its exact quantity column may differ by deployment.
//  */
// final class DatabaseInventoryStockInService implements InventoryStockInContract
// {
//     public function post(GoodsReceipt $receipt, array $items): void
//     {
//         foreach ($items as $item) {
//             $this->postItem($receipt, $item, false);
//         }
//     }

//     public function reverse(GoodsReceipt $receipt, array $items): void
//     {
//         foreach ($items as $item) {
//             $this->postItem($receipt, $item, true);
//         }
//     }

//     private function postItem(GoodsReceipt $receipt, array $item, bool $reverse): void
//     {
//         $variantId = $item['product_variant_id'] ?? null;
//         $productId = (int) $item['product_id'];
//         $qty = (float) $item['accepted_quantity'];

//         if ($qty <= 0) {
//             return;
//         }

//         $query = DB::table('inventory_stocks');
//         if ($variantId !== null && Schema::hasColumn('inventory_stocks', 'product_variant_id')) {
//             $query->where('product_variant_id', $variantId);
//         } else {
//             $query->where('product_id', $productId);
//         }

//         $stock = $query->lockForUpdate()->first();
//         if (!$stock) {
//             if ($reverse) {
//                 throw new RuntimeException('Inventory stock record not found while reversing GRN ' . $receipt->grn_number . '.');
//             }
//             $data = $this->buildStockInsert($productId, $variantId, $qty);
//             $stockId = DB::table('inventory_stocks')->insertGetId($data);
//             $before = 0.0;
//             $after = $qty;
//         } else {
//             $quantityColumn = $this->quantityColumn('inventory_stocks');
//             $before = (float) ($stock->{$quantityColumn} ?? 0);
//             $after = $reverse ? $before - $qty : $before + $qty;
//             if ($after < -0.0001) {
//                 throw new RuntimeException('Inventory stock cannot become negative while reversing GRN ' . $receipt->grn_number . '.');
//             }
//             $after = max(0, $after);
//             DB::table('inventory_stocks')->where('id', $stock->id)->update([
//                 $quantityColumn => $after,
//                 ...($this->hasColumn('inventory_stocks', 'updated_at') ? ['updated_at' => now()] : []),
//             ]);
//             $stockId = $stock->id;
//         }

//         $this->insertTransaction($receipt, $item, $stockId, $before, $after, $reverse);

//     }

//     private function buildStockInsert(int $productId, ?int $variantId, float $qty): array
//     {
//         $data = [];
//         if ($this->hasColumn('inventory_stocks', 'uuid')) $data['uuid'] = (string) \Illuminate\Support\Str::uuid();
//         if ($this->hasColumn('inventory_stocks', 'product_id')) $data['product_id'] = $productId;
//         if ($variantId !== null && $this->hasColumn('inventory_stocks', 'product_variant_id')) $data['product_variant_id'] = $variantId;
//         $quantityColumn = $this->quantityColumn('inventory_stocks');
//         $data[$quantityColumn] = $qty;
//         if ($this->hasColumn('inventory_stocks', 'is_active')) $data['is_active'] = true;
//         if ($this->hasColumn('inventory_stocks', 'created_at')) $data['created_at'] = now();
//         if ($this->hasColumn('inventory_stocks', 'updated_at')) $data['updated_at'] = now();
//         return $data;
//     }

//     private function insertTransaction(GoodsReceipt $receipt, array $item, int $stockId, float $before, float $after, bool $reverse = false): void
//     {
//         if (!Schema::hasTable('inventory_transactions')) {
//             throw new RuntimeException('inventory_transactions table is not available.');
//         }

//         $data = [];
//         $put = function (string $column, mixed $value) use (&$data): void {
//             if (Schema::hasColumn('inventory_transactions', $column)) $data[$column] = $value;
//         };

//         //$put('uuid', (string) \Illuminate\Support\Str::uuid());
//         $put('inventory_stock_id', $stockId);
//         $put('product_id', (int)$item['product_id']);
//          $put('product_variant_id', $item['product_variant_id'] ?? null);
//          $put('quantity', (float)$item['accepted_quantity']);
//          $put('type', $reverse ? 'purchase_void' : 'purchase');
//         $put('transaction_type', $reverse ? 'purchase_void' : 'purchase');
//         //$put('direction', $reverse ? 'out' : 'in');
//             $put('reference_type', $reverse ? 'purchase_grn_void' : 'purchase_order');
//             $put('reference_id', $receipt->purchaseOrder?->po_number ?? $receipt->grn_number);
//         //$put('reference_uuid', $receipt->uuid);
//         $put('notes', ($reverse ? 'Stock reversed from ' : 'Stock received through ') . $receipt->grn_number);
//         $put('quantity_before', $before);
//         $put('quantity_after', $after);
//         //$put('balance_before', $before);
//         //$put('balance_after', $after);
//         $put('created_at', now());
//         $put('updated_at', now());

//         DB::table('inventory_transactions')->insert($data);
//     }

//     private function quantityColumn(string $table): string
//     {
//         foreach (['quantity','stock_quantity','current_stock','on_hand_quantity','available_quantity'] as $column) {
//             if ($this->hasColumn($table, $column)) return $column;
//         }
//         throw new RuntimeException("No supported stock quantity column found on {$table}.");
//     }

//     private function hasColumn(string $table, string $column): bool
//     {
//         return Schema::hasColumn($table, $column);
//     }
// }


declare(strict_types=1);

namespace App\Modules\Purchase\Services;

use App\Modules\Inventory\Models\InventoryTransaction;
use App\Modules\Inventory\Repositories\InventoryStockRepository;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Purchase\Contracts\InventoryStockInContract;
use App\Modules\Purchase\Models\GoodsReceipt;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class DatabaseInventoryStockInService implements InventoryStockInContract
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly InventoryStockRepository $inventoryStockRepository
    ) {
    }

    /**
     * Post accepted GRN quantities into inventory.
     *
     * GRN:
     *
     * Purchase
     *      ↓
     * Stock IN
     */
    public function post(
        GoodsReceipt $receipt,
        array $items,
        ?int $createdBy = null
    ): void {
        DB::transaction(function () use ($receipt, $items, $createdBy): void {
            foreach ($items as $item) {
                $this->postItem(
                    receipt: $receipt,
                    item: $item,
                    reverse: false,
                    createdBy: $createdBy
                );
            }
        });
    }

    /**
     * Reverse previously posted GRN quantities.
     *
     * GRN reversal:
     *
     * Purchase
     *      ↓
     * Stock OUT
     */
    public function reverse(
        GoodsReceipt $receipt,
        array $items,
        ?int $createdBy = null
    ): void {
        DB::transaction(function () use ($receipt, $items, $createdBy): void {
            foreach ($items as $item) {
                $this->postItem(
                    receipt: $receipt,
                    item: $item,
                    reverse: true,
                    createdBy: $createdBy
                );
            }
        });
    }

    /**
     * Process one GRN item.
     *
     * @param array<string, mixed> $item
     */
    private function postItem(
        GoodsReceipt $receipt,
        array $item,
        bool $reverse,
        ?int $createdBy
    ): void {
        $productId = (int) ($item["product_id"] ?? 0);

        $variantId = isset($item["product_variant_id"])
            ? (int) $item["product_variant_id"]
            : null;

        $quantity = (float) ($item["accepted_quantity"] ?? 0);

        if ($productId <= 0) {
            throw new RuntimeException(
                "Invalid product ID in GRN {$receipt->grn_number}."
            );
        }

        if ($quantity <= 0) {
            return;
        }

        /*
         * Find existing inventory stock.
         */
        $stock = $this->inventoryStockRepository->findByProductAndVariant(
            productId: $productId,
            productVariantId: $variantId
        );

        if (!$stock) {
            $description =
                $variantId !== null
                    ? "product {$productId}, variant {$variantId}"
                    : "product {$productId}";

            throw new RuntimeException(
                "Inventory stock record not found for {$description} " .
                    "while processing GRN {$receipt->grn_number}."
            );
        }

        /*
         * GRN Receive
         *
         * Purchase → Inventory IN
         */
        if (!$reverse) {
            $this->inventoryService->stockIn(
                uuid: $stock->uuid,
                quantity: $quantity,
                type: InventoryTransaction::TYPE_PURCHASE,
                referenceType: "purchase_grn",
                referenceId: $this->referenceId($receipt),
                notes: "Stock received through {$receipt->grn_number}",
                createdBy: $createdBy
            );

            return;
        }

        /*
         * GRN Reversal
         *
         * Purchase Cancellation → Inventory OUT
         */
        $this->inventoryService->stockOut(
            uuid: $stock->uuid,
            quantity: $quantity,
            type: InventoryTransaction::TYPE_CANCELLATION,
            referenceType: "purchase_grn_void",
            referenceId: $this->referenceId($receipt),
            notes: "Stock reversed from {$receipt->grn_number}",
            createdBy: $createdBy
        );
    }

    /**
     * Get purchase order number as reference.
     *
     * Fallback to GRN number when purchase order is unavailable.
     */
    private function referenceId(GoodsReceipt $receipt): string
    {
        return $receipt->purchaseOrder?->po_number ?? $receipt->grn_number;
    }
}


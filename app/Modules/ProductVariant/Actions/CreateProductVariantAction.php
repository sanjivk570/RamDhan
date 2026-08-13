<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Actions;

use App\Modules\ProductVariant\Services\ProductVariantService;
use App\Modules\Inventory\Services\InventoryService;
use Illuminate\Support\Facades\DB;


class CreateProductVariantAction
{
    public function __construct
    (
        private readonly ProductVariantService $service,
        private readonly InventoryService $inventoryService
    )
    {
    }

    public function execute(string $productUuid, array $data)
    {
        //return $this->service->create($productUuid, $data);

        // Create product first
         return DB::transaction(function () use ($productUuid, $data) {

            /*
             * Create product first.
             */
            $productVariant = $this->service->create($productUuid, $data);

            /*
             * Automatically create initial
             * inventory stock.
             *
             * quantity = 0
             * reserved_quantity = 0
             * low_stock_threshold =
             *     product.low_stock_threshold
             */
            $this->inventoryService
                ->createInitialStockForProductVariant($productVariant, $data);

            return $productVariant;
        });
    }
}

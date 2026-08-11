<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;
use App\Modules\Inventory\Services\InventoryService;

/**
 * Create a new product.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CreateProductAction
{
    /**
     * CreateProductAction constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService,
        private readonly InventoryService $inventoryService
    ) {
    }

    /**
     * Execute the product creation.
     *
     * @param array $data
     * @return mixed
     */
    public function execute(array $data)
    {
        //return $this->productService->create($data);

        // Create product first
        $product = $this->productService
            ->create($data);

        /*
            * Automatically create initial
            * inventory stock.
            *
            * quantity = 0
            * reserved_quantity = 0
            * low_stock_threshold =
            *     product.low_stock_threshold
            */
        $this->inventoryService->createInitialStockForProduct($product);

        return $product;
    }
}
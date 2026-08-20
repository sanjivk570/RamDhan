<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;
use App\Modules\Inventory\Services\InventoryService;

/**
 * Update an existing product.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateProductAction
{

    /**
     * UpdateProductAction constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService,
        private readonly InventoryService $inventoryService
    ) {
    }

    /**
     * Execute product update.
     *
     * @param string $uuid
     * @param array $data
     * @return mixed
     */
    public function execute(string $uuid, array $data) {
        //return $this->productService->update($uuid,$data);
        $product = $this->productService->update($uuid, $data);

        //Update invetory also
        $this->inventoryService->updateInitialStockForProduct($product, $data);
        $product->refresh();
        return $product;

    }
}
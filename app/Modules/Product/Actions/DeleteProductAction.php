<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * Delete a product.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class DeleteProductAction
{
    /**
     * DeleteProductAction constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute product deletion.
     *
     * @param string $uuid
     * @return void
     */
    public function execute(string $uuid): void
    {
        $this->productService->delete($uuid);
    }
}
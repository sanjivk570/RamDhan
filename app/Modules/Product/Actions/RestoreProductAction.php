<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * Restore a deleted product.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class RestoreProductAction
{
    /**
     * RestoreProductAction constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute product restoration.
     *
     * @param string $uuid
     * @return mixed
     */
    public function execute(string $uuid)
    {
        return $this->productService->restore($uuid);
    }
}
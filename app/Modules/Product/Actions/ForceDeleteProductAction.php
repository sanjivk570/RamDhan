<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

class ForceDeleteProductAction
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Permanently delete a product.
     */
    public function execute(string $uuid): void
    {
        $this->productService
            ->forceDelete($uuid);
    }
}
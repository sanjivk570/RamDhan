<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * Show an active product from the public storefront catalog.
 *
 * @package App\Modules\Product\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ShowStorefrontProductAction
{
    /**
     * Create a new show storefront product action.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute storefront product details retrieval.
     *
     * @param string $uuid
     * @return \App\Modules\Product\Models\Product
     */
    public function execute(string $uuid)
    {
        return $this->productService->storefrontDetails($uuid);
    }
}
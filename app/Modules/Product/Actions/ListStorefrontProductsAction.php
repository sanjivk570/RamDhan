<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * List active products for the public storefront catalog.
 *
 * @package App\Modules\Product\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ListStorefrontProductsAction
{
    /**
     * Create a new list storefront products action.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute storefront product listing.
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return $this->productService->storefrontList($filters);
    }
}
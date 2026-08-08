<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * List products with filters.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ListProductAction
{
    /**
     * ListProductAction constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute product listing.
     *
     * @param array $filters
     * @return mixed
     */
    public function execute(array $filters)
    {
        return $this->productService->list($filters);
    }
}
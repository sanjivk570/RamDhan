<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * Show product details.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ShowProductAction
{

    /**
     * ShowProductAction constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute product details retrieval.
     *
     * @param string $uuid
     * @return mixed
     */
    public function execute(string $uuid)
    {
        return $this->productService->details($uuid);
    }
}
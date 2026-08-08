<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

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
        private readonly ProductService $productService
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
        return $this->productService->create($data);
    }
}
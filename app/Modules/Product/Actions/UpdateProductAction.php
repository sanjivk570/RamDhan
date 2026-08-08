<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

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
        private readonly ProductService $productService
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
        return $this->productService->update($uuid,$data);
    }
}
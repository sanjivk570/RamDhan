<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductService;

/**
 * Change the active status of a product.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ChangeStatusAction
{
    
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Execute the product status change.
     *
     * @param string $uuid
     * @param bool $status
     * @return mixed
     */
    public function execute(string $uuid, bool $status
    ) {
        return $this->productService->changeStatus($uuid, $status);
    }
}
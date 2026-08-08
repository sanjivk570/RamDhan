<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductImageService;

/**
 * Delete a product image.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class DeleteProductImageAction
{
    /**
     * DeleteProductImageAction constructor.
     *
     * @param ProductImageService $productImageService
     */
    public function __construct(
        private readonly ProductImageService $productImageService
    ) {
    }

    /**
     * Execute product image deletion.
     *
     * @param string $productUuid
     * @param string $imageUuid
     * @return void
     */
    public function execute(string $productUuid, string $imageUuid): void {
        $this->productImageService->delete($productUuid, $imageUuid);
    }
}
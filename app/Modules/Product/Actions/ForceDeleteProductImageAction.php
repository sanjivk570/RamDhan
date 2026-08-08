<?php

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductImageService;

/**
 * Permanently delete a product image.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ForceDeleteProductImageAction
{
    /**
     * ForceDeleteProductImageAction constructor.
     *
     * @param ProductImageService $productImageService
     */
    public function __construct(
        private readonly ProductImageService $productImageService
    ) {
    }

    /**
     * Execute permanent product image deletion.
     *
     * @param string $uuid
     * @param string $imageUuid
     * @return bool
     */
    public function execute(string $uuid, string $imageUuid): bool
    {
        return $this->productImageService->forceDelete($uuid, $imageUuid);
    }
}
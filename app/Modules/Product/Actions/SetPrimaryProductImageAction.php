<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Models\ProductImage;
use App\Modules\Product\Services\ProductImageService;

/**
 * Set a product image as the primary image.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class SetPrimaryProductImageAction
{

    /**
     * SetPrimaryProductImageAction constructor.
     *
     * @param ProductImageService $productImageService
     */
    public function __construct(
        private readonly ProductImageService $productImageService
    ) {
    }

    /**
     * Execute primary image update.
     *
     * @param string $productUuid
     * @param string $imageUuid
     * @return ProductImage
     */
    public function execute(string $productUuid, string $imageUuid): ProductImage 
    {
        return $this->productImageService->setPrimary($productUuid, $imageUuid);
    }
}
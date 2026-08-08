<?php

declare(strict_types=1);

namespace App\Modules\Product\Actions;

use App\Modules\Product\Services\ProductImageService;
use App\Modules\Product\Models\ProductImage;

/**
 * Create a new product image.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CreateProductImageAction
{
    /**
     * CreateProductImageAction constructor.
     *
     * @param ProductImageService $productImageService
     */
    public function __construct(
        private readonly ProductImageService $productImageService
    ) {
    }

    /**
     * Execute product image creation.
     *
     * @param string $productUuid
     * @param array $data
     * @return ProductImage
     */
    public function execute(string $productUuid, array $data): ProductImage 
    {
        return $this->productImageService->create($productUuid,$data);
    }
}
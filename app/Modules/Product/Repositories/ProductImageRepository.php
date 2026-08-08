<?php

declare(strict_types=1);

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository responsible for product image data operations.
 *
 * Handles product image retrieval, creation, deletion,
 * primary image management, and related queries.
 *
 * @package App\Modules\Product\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
class ProductImageRepository
{

    /**
     * Find product by UUID.
     *
     * @param string $uuid
     * @return Product|null
     */
    public function findProductByUuid(string $uuid): ?Product
    {
        return Product::where('uuid', $uuid)->first();
    }

    /**
     * Find product image by UUID.
     *
     * @param string $uuid
     * @return ProductImage|null
     */
    public function findByUuid(string $uuid): ?ProductImage 
    {
        return ProductImage::where('uuid', $uuid)->first();
    }

    /**
     * Find product image by UUID
     * belonging to a specific product.
     *
     * @param string $imageUuid
     * @param int $productId
     * @return ProductImage|null
     */
    public function findByUuidAndProduct(string $imageUuid, int $productId): ?ProductImage 
    {
        return ProductImage::where('uuid', $imageUuid)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Create a product image.
     *
     * @param array $data
     * @return ProductImage
     */
    public function create(array $data): ProductImage
    {
        return ProductImage::create($data);
    }

    /**
     * Get product images.
     *
     * @param int $productId
     * @return Collection<int, ProductImage>
     */
    public function getByProduct(int $productId): Collection 
    {
        return ProductImage::where('product_id', $productId)->orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * Set all product images as non-primary.
     *
     * @param int $productId
     * @return void
     */
    public function clearPrimary(int $productId): void 
    {
        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
    }

    /**
     * Set image as primary.
     *
     * @param ProductImage $image
     * @return ProductImage
     */
    public function setPrimary(ProductImage $image): ProductImage 
    {
        $image->update(['is_primary' => true]);
        return $image->refresh();
    }

    /**
     * Delete product image.
     *
     * @param ProductImage $image
     * @return bool
     */
    public function delete(ProductImage $image): bool 
    {
        return (bool) $image->delete();
    }

    /**
     * Get first remaining image.
     *
     * @param int $productId
     * @return ProductImage|null
     */
    public function getFirstImage(int $productId): ?ProductImage 
    {
        return ProductImage::where('product_id', $productId)->orderBy('sort_order')->orderBy('id')->first();
    }

    /**
     * Check whether product has a primary image.
     *
     * @param int $productId
     * @return bool
     */
    public function hasPrimary(int $productId): bool {
        return ProductImage::where('product_id',$productId)->where('is_primary', true)->exists();
    }

    /**
     * Permanently delete a product image.
     *
     * @param ProductImage $productImage
     * @return bool
     */
    public function forceDelete(ProductImage $productImage): bool {
        return (bool) $productImage->forceDelete();
    }

}

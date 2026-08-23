<?php

declare(strict_types=1);

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Category\Models\Category;
use App\Modules\Media\Services\MediaService;
use RuntimeException;
use Illuminate\Support\Facades\DB;

/**
 * Service responsible for product business operations.
 *
 * Handles product listing, retrieval, creation, updating,
 * category synchronization, status changes, deletion,
 * and restoration.
 *
 * @package App\Modules\Product\Services
 * @author Sanjiv Kumar Kushwaha
 */
class ProductService
{

    /**
     * Create a new product service.
     *
     * @param ProductRepository $productRepository
     */
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly MediaService $mediaService
    ) {
    }

    /**
     * Retrieve paginated products.
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters)
    {
        return $this->productRepository->paginate($filters);
    }

    /**
     * Retrieve product details by UUID.
     *
     * @param string $uuid
     * @return Product
     */
    public function details(string $uuid): Product
    {
        return $this->productRepository->findByUuidOrFail($uuid);
    }

    /**
     * Retrieve paginated active products for the storefront (public catalog).
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function storefrontList(array $filters)
    {
        return $this->productRepository->paginateStorefront($filters);
    }

    /**
     * Retrieve an active (published) product by UUID for the storefront.
     *
     * @param string $uuid
     * @return Product
     */
    public function storefrontDetails(string $uuid): Product
    {
        return $this->productRepository->findActiveByUuid($uuid);
    }

    /**
     * Create a new product.
     *
     * Category UUIDs are resolved to category IDs
     * before synchronizing the product categories.
     *
     * @param array<string, mixed> $data
     * @return Product
     */
    public function create(array $data): Product
    {
        //$categories = $data['categories'] ?? [];
        $categoryUuids = $data["categories"] ?? [];

        unset($data["categories"]);

        $product = $this->productRepository->create($data);

        if (!empty($categoryUuids)) {
            $categoryIds = Category::query()
                ->whereIn("uuid", $categoryUuids)
                ->pluck("id")
                ->toArray();

            $this->productRepository->syncCategories($product, $categoryIds);
        }

        return $product->load(["categories", "images"]);
    }

    /**
     * Update an existing product.
     *
     * Categories are synchronized only when the
     * categories field is included in the request.
     *
     * @param string $uuid
     * @param array<string, mixed> $data
     * @return Product
     */
    public function update(string $uuid, array $data): Product
    {
        $product = $this->productRepository->findByUuidOrFail($uuid);

        //$categories = $data['categories'] ?? null;
        $categoryUuids = $data["categories"] ?? [];

        unset($data["categories"]);

        $product = $this->productRepository->update($product, $data);

        if ($categoryUuids !== null) {
            $categoryIds = Category::query()
                ->whereIn("uuid", $categoryUuids)
                ->pluck("id")
                ->toArray();

            $this->productRepository->syncCategories($product, $categoryIds);
        }

        return $product->load(["categories", "images"]);
    }

    /**
     * Change product active status.
     *
     * @param string $uuid
     * @param bool $status
     * @return Product
     */
    public function changeStatus(string $uuid, bool $status): Product
    {
        $product = $this->productRepository->findByUuidOrFail($uuid);

        return $this->productRepository->changeStatus($product, $status);
    }

    /**
     * Soft delete a product.
     *
     * @param string $uuid
     * @return void
     */
    public function delete(string $uuid): void
    {
        $product = $this->productRepository->findByUuidOrFail($uuid);
        //$this->productRepository->delete($product);

        // DB::transaction(function () use ($product) {
            $this->productRepository->delete($product);

            $this->mediaService->deleteByMediable($product->getMorphClass(), $product->id);
        //});
    }

    /**
     * Restore a deleted product.
     *
     * @param string $uuid
     * @return Product
     */
    public function restore(string $uuid): Product
    {
        $product = $this->productRepository->findWithTrashedByUuidOrFail($uuid);

        if (!$product->trashed()) {
            throw new RuntimeException(
                'Product is already active.'
            );
        }

        $this->productRepository->restore($product->uuid);
        $this->mediaService->restoreByMediable($product->getMorphClass(), $product->id);

        return $product->refresh();
    }

    /**
     * Permanently delete a product and all related media.
     *
     * @param string $uuid
     * @return void
     */
    public function forceDelete(string $uuid): void
    {
        $product = $this->productRepository
            ->findWithTrashedByUuidOrFail($uuid);

        if (!$product->trashed()) {
            throw new RuntimeException(
                'Product must be deleted before permanent deletion.'
            );
        }

        DB::transaction(function () use ($product) {

            /*
            * Permanently delete all product media.
            *
            * MediaService is responsible for deleting
            * both database records and physical files.
            */
            $this->mediaService
                ->forceDeleteByMediable(
                    $product->getMorphClass(),
                    $product->id
                );

            /*
            * Permanently delete the product.
            */
            $this->productRepository
                ->forceDelete($product);
        });
    }

}

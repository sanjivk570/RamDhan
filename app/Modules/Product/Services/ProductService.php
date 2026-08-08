<?php

declare(strict_types=1);

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Category\Models\Category;

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
        private readonly ProductRepository $productRepository
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

        $this->productRepository->delete($product);
    }

    /**
     * Restore a deleted product.
     *
     * @param string $uuid
     * @return Product
     */
    public function restore(string $uuid): Product
    {
        return $this->productRepository->restore($uuid);
    }
}

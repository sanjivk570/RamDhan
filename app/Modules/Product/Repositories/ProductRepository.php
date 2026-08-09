<?php

declare(strict_types=1);

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for product data operations.
 *
 * Handles product retrieval, filtering, creation, updating,
 * status changes, deletion, restoration, categories, and images.
 *
 * @package App\Modules\Product\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
class ProductRepository
{

    /**
     * Retrieve paginated products with optional filters.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Product::query()
        //->with(['categories', 'images'])
        ->with([
            'categories',
            'images' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])

            /*
             * Global Search
             */
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('sku', 'LIKE', "%{$search}%")
                        ->orWhere('slug', 'LIKE', "%{$search}%"
                        );
                    });
                }
            )

            /*
             * Column Filters
             */
            ->when(
                !empty($filters['filters']['name']),
                function ($query) use ($filters) {
                    $query->where('name', 'LIKE', '%' . $filters['filters']['name'] . '%');
                }
            )

            ->when(
                !empty($filters['filters']['sku']),
                function ($query) use ($filters) {
                    $query->where('sku', 'LIKE', '%' . $filters['filters']['sku'] . '%');
                }
            )

            ->when(
                !empty($filters['filters']['slug']),
                function ($query) use ($filters) {
                    $query->where('slug', 'LIKE', '%' . $filters['filters']['slug'] . '%');
                }
            )

            ->when(
                isset($filters['filters']['category']) && $filters['filters']['category'] !== '',
                function ($query) use ($filters) {
                    $query->whereHas('categories',
                        function ($categoryQuery) use ($filters) {
                            $categoryQuery->where('uuid', $filters['filters']['category']);
                        }
                    );
                }
            )

            ->when(
                isset($filters['filters']['is_active']) && $filters['filters']['is_active'] !== '',
                function ($query) use ($filters) {
                    $query->where('is_active', (bool) $filters['filters']['is_active']);
                }
            )

            ->when(
                isset($filters['filters']['is_featured']) && $filters['filters']['is_featured'] !== '',
                function ($query) use ($filters) {
                    $query->where('is_featured', (bool) $filters['filters']['is_featured']);
                }
            )

            ->when(
                isset($filters['filters']['min_price']) && $filters['filters']['min_price'] !== '',
                function ($query) use ($filters) {
                    $query->where('price', '>=', $filters['filters']['min_price']);
                }
            )

            ->when(
                isset($filters['filters']['max_price']) && $filters['filters']['max_price'] !== '',
                function ($query) use ($filters) {
                    $query->where( 'price', '<=', $filters['filters']['max_price']);
                }
            )
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')

            ->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Find product by UUID.
     *
     * @param string $uuid
     * @return Product|null
     */
    public function findByUuid(string $uuid): ?Product
    {
        // return Product::with(['categories', 'images'])->where('uuid', $uuid)->first();

        return Product::with(
            ['categories', 'images' => 
            function ($query) {
                $query->orderBy('sort_order'); },
            ])->where('uuid', $uuid)->first();
    }

    /**
     * Find product by UUID.
     *
     * @param string $uuid
     * @return Product|null
     */
    public function findByUuidOrFail(string $uuid): Product
    {
        // return Product::with(['categories', 'images'])->where('uuid', $uuid)->firstOrFail();

        return Product::with(['categories', 'images' => 
            function ($query) {
                $query->orderBy('sort_order'); },
            ])->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new product.
     *
     * @param array<string, mixed> $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array<string, mixed> $data
     * @return Product
     */
    public function update(Product $product, array $data): Product 
    {
        $product->update($data);
        return $product->refresh();
    }

    /**
     * Change the active status of a product.
     *
     * @param Product $product
     * @param bool $status
     * @return Product
     */
    public function changeStatus(Product $product, bool $status): Product 
    {
        $product->update(['is_active' => $status]);
        return $product->refresh();
    }

    /**
     * Soft delete a product.
     *
     * @param Product $product
     * @return bool
     */
    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    /**
     * Restore a deleted product.
     *
     * @param string $uuid
     * @return Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function restore(string $uuid): Product
    {
        $product = Product::withTrashed()->with(['categories', 'images'])->where('uuid', $uuid)->firstOrFail();
        $product->restore();
        return $product->refresh();
    }

    /**
     * Synchronize product categories.
     *
     * @param Product $product
     * @param array<int, int|string> $categoryIds
     * @return void
     */
    public function syncCategories(Product $product, array $categoryIds): void 
    { 
        $product->categories()->sync($categoryIds);
    }

    /**
     * Create a product image.
     *
     * @param Product $product
     * @param array<string, mixed> $data
     * @return \App\Modules\Product\Models\ProductImage
     */
    public function createImage(Product $product, array $data) {
        return $product->images()->create($data);
    }

    /**
     * Delete all images associated with a product.
     *
     * @param Product $product
     * @return void
     */
    public function deleteImages(Product $product): void 
    {
        $product->images()->delete();
    }

    public function findWithTrashedByUuidOrFail(string $uuid): Product 
    {
        return Product::withTrashed()->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Permanently delete a product.
     */
    public function forceDelete(Product $product): bool
    {
        return (bool) $product->forceDelete();
    }
}
<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Repositories;

use App\Modules\Wishlist\Models\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for wishlist data access operations.
 *
 * Handles database interactions related to wishlist records including
 * paginated listing with filters, retrieval, creation, and deletion.
 *
 * @package App\Modules\Wishlist\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class WishlistRepository
{
    /**
     * Retrieve a paginated list of wishlist records with optional filters.
     *
     * Supports global search across product name/code, nested "filters"
     * (or legacy flat keys), sorting, and pagination.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $f = $filters['filters'] ?? $filters;

        return Wishlist::query()
            ->with(['customer', 'product', 'variant'])

            // Global search across the customer and the wish-listed product
            ->when(
                $filters['search'] ?? null,
                function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->whereHas('customer', fn ($c) => $c
                            ->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%"))
                            ->orWhereHas('product', fn ($p) => $p->where('name', 'LIKE', "%{$search}%"));
                    });
                }
            )

            // Column Filters
            ->when(
                isset($f['customer_id']) && $f['customer_id'] !== '',
                fn ($q) => $q->where('customer_id', $f['customer_id'])
            )

            ->when(
                isset($f['product_id']) && $f['product_id'] !== '',
                fn ($q) => $q->where('product_id', $f['product_id'])
            )

            ->when(
                isset($f['product_variant_id']) && $f['product_variant_id'] !== '',
                fn ($q) => $q->where('product_variant_id', $f['product_variant_id'])
            )

            ->when(
                $f['from_date'] ?? null,
                fn ($q, $date) => $q->whereDate('created_at', '>=', $date)
            )

            ->when(
                $f['to_date'] ?? null,
                fn ($q, $date) => $q->whereDate('created_at', '<=', $date)
            )

            ->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_order'] ?? 'desc'
            )

            ->paginate(
                $filters['per_page'] ?? 20
            );
    }

    /**
     * Find a wishlist record by UUID.
     *
     * @param string $uuid The wishlist UUID.
     * @return Wishlist|null
     */
    public function findByUuid(string $uuid): ?Wishlist
    {
        return Wishlist::with(['product', 'variant'])
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Find a wishlist record by UUID or throw an exception.
     *
     * @param string $uuid The wishlist UUID.
     * @return Wishlist
     */
    public function findByUuidOrFail(string $uuid): Wishlist
    {
        return Wishlist::with(['product', 'variant'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * Create a new wishlist record.
     *
     * @param array<string, mixed> $data The wishlist data.
     * @return Wishlist
     */
    public function create(array $data): Wishlist
    {
        return Wishlist::create($data);
    }

    /**
     * Delete a wishlist record.
     *
     * @param Wishlist $wishlist The wishlist instance.
     * @return bool
     */
    public function delete(Wishlist $wishlist): bool
    {
        return (bool) $wishlist->delete();
    }
}
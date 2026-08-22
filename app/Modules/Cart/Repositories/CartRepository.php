<?php

declare(strict_types=1);

namespace App\Modules\Cart\Repositories;

use App\Modules\Cart\Models\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CartRepository
{
    /**
     * Retrieve a paginated list of carts with optional filters.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Cart::query()
            ->with("items")

            ->when(!empty($filters["customer_id"]), function ($query) use (
                $filters
            ) {
                $query->where("customer_id", $filters["customer_id"]);
            })

            ->when(!empty($filters["status"]), function ($query) use (
                $filters
            ) {
                $query->where("status", $filters["status"]);
            })

            ->latest()

            ->paginate($filters["per_page"] ?? 20);
    }

    /**
     * Find a cart by UUID.
     *
     * @param string $uuid
     * @return Cart|null
     */
    public function findByUuid(string $uuid): ?Cart
    {
        return Cart::with("items")
            ->where("uuid", $uuid)
            ->first();
    }

    /**
     * Find a cart by UUID or throw an exception.
     *
     * @param string $uuid
     * @return Cart
     */
    public function findByUuidOrFail(string $uuid): Cart
    {
        return Cart::with("items")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
}

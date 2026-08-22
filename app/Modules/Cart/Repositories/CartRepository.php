<?php

declare(strict_types=1);

namespace App\Modules\Cart\Repositories;

use App\Modules\Cart\Models\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for cart data access operations.
 *
 * Handles database interactions related to carts including paginated
 * listing with filters, retrieval, creation, updating, status changes,
 * soft deletion, and restoration.
 *
 * @package App\Modules\Cart\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class CartRepository
{
    /**
     * Retrieve a paginated list of carts with optional filters.
     *
     * Supports global search, nested "filters" (or legacy flat keys),
     * sorting, and pagination.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $f = $filters['filters'] ?? $filters;

        return Cart::query()
            ->with(['items', 'customer'])

            // Global search across guest token, coupon code and customer details
            ->when(
                $filters['search'] ?? null,
                function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('guest_token', 'LIKE', "%{$search}%")
                            ->orWhere('coupon_code', 'LIKE', "%{$search}%")
                            ->orWhereHas('customer', fn ($c) => $c
                                ->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%")
                                ->orWhere('mobile', 'LIKE', "%{$search}%"));
                    });
                }
            )

            // Column Filters
            ->when(
                isset($f['customer_id']) && $f['customer_id'] !== '',
                function ($q) use ($f) {
                    $q->where('customer_id', $f['customer_id']);
                }
            )

            ->when(
                !empty($f['status']),
                fn ($q) => $q->where('status', $f['status'])
            )

            ->when(
                !empty($f['coupon_code']),
                fn ($q) => $q->where('coupon_code', 'LIKE', '%' . $f['coupon_code'] . '%')
            )

            ->when(
                !empty($f['currency_code']),
                fn ($q) => $q->where('currency_code', strtoupper($f['currency_code']))
            )

            ->when(
                $f['from_date'] ?? null,
                fn ($q, $date) => $q->whereDate('created_at', '>=', $date)
            )

            ->when(
                $f['to_date'] ?? null,
                fn ($q, $date) => $q->whereDate('created_at', '<=', $date)
            )

            ->when(
                isset($f['min_total']) && $f['min_total'] !== '',
                fn ($q) => $q->where('grand_total', '>=', (float) $f['min_total'])
            )

            ->when(
                isset($f['max_total']) && $f['max_total'] !== '',
                fn ($q) => $q->where('grand_total', '<=', (float) $f['max_total'])
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
     * Find a cart by database ID.
     *
     * @param int $id The cart ID.
     * @return Cart|null
     */
    public function findById(int $id): ?Cart
    {
        return Cart::with(['items', 'customer'])->find($id);
    }

    /**
     * Find a cart by UUID.
     *
     * @param string $uuid The cart UUID.
     * @return Cart|null
     */
    public function findByUuid(string $uuid): ?Cart
    {
        return Cart::with(['items', 'customer'])
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Find a cart by UUID or throw an exception.
     *
     * @param string $uuid The cart UUID.
     * @return Cart
     */
    public function findByUuidOrFail(string $uuid): Cart
    {
        return Cart::with(['items', 'customer'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * Create a new cart.
     *
     * @param array<string, mixed> $data The cart data.
     * @return Cart
     */
    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    /**
     * Update an existing cart.
     *
     * @param Cart $cart The cart instance.
     * @param array<string, mixed> $data The updated cart data.
     * @return Cart
     */
    public function update(Cart $cart, array $data): Cart
    {
        $cart->update($data);

        return $cart->refresh();
    }

    /**
     * Change the status of a cart.
     *
     * @param Cart $cart The cart instance.
     * @param string $status The new cart status.
     * @return Cart
     */
    public function changeStatus(Cart $cart, string $status): Cart
    {
        $cart->update(['status' => $status]);

        return $cart->refresh();
    }

    /**
     * Soft delete a cart.
     *
     * @param Cart $cart The cart instance.
     * @return bool
     */
    public function delete(Cart $cart): bool
    {
        return (bool) $cart->delete();
    }

    /**
     * Restore a soft-deleted cart.
     *
     * @param string $uuid The cart UUID.
     * @return Cart
     */
    public function restore(string $uuid): Cart
    {
        $cart = Cart::withTrashed()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $cart->restore();

        return $cart->refresh();
    }
}

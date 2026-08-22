<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Repositories;

use App\Modules\Promotion\Models\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for coupon data access operations.
 *
 * Handles database interactions related to coupons including paginated
 * listing with filters, retrieval, creation, updating, status changes,
 * and soft deletion.
 *
 * @package App\Modules\Promotion\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class CouponRepository
{
    /**
     * Retrieve a paginated list of coupons with optional filters.
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

        return Coupon::query()

            // Global search across coupon code and name
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })

            // Column Filters
            ->when(!empty($f['discount_type']), fn ($q) => $q->where('discount_type', $f['discount_type']))
            ->when(isset($f['is_active']) && $f['is_active'] !== '', fn ($q) => $q->where('is_active', (bool) $f['is_active']))

            // Active window filters (starts_at / ends_at overlap)
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->where(function ($query) use ($date) {
                $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', $date);
            }))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->where(function ($query) use ($date) {
                $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', $date);
            }))
            ->when($f['created_from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($f['created_to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))

            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findById(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    public function findByUuid(string $uuid): ?Coupon
    {
        return Coupon::where('uuid', $uuid)->first();
    }

    public function findByUuidOrFail(string $uuid): Coupon
    {
        return Coupon::where('uuid', $uuid)->firstOrFail();
    }

    public function findByCode(string $code): ?Coupon
    {
        return Coupon::whereRaw('UPPER(code)=?', [strtoupper(trim($code))])->first();
    }

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon->refresh();
    }

    public function changeStatus(Coupon $coupon, bool $status): Coupon
    {
        $coupon->update(['is_active' => $status]);

        return $coupon->refresh();
    }

    public function delete(Coupon $coupon): bool
    {
        return (bool) $coupon->delete();
    }
}
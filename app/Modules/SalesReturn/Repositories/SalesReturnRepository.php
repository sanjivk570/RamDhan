<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Repositories;

use App\Modules\SalesReturn\Models\SalesReturn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for sales return data access operations.
 *
 * Handles database interactions related to sales returns including
 * paginated listing with filters, retrieval, creation, and updating.
 *
 * @package App\Modules\SalesReturn\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class SalesReturnRepository
{
    /**
     * Retrieve a paginated list of returns with optional filters.
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

        return SalesReturn::query()
            ->with(['items', 'order', 'customer'])

            // Global search across return/order number and customer details
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('return_number', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn ($c) => $c
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })

            // Column Filters
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['refund_status']), fn ($q) => $q->where('refund_status', $f['refund_status']))
            ->when(isset($f['order_id']) && $f['order_id'] !== '', fn ($q) => $q->where('order_id', $f['order_id']))
            ->when(isset($f['customer_id']) && $f['customer_id'] !== '', fn ($q) => $q->where('customer_id', $f['customer_id']))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when(isset($f['min_total']) && $f['min_total'] !== '', fn ($q) => $q->where('total_amount', '>=', (float) $f['min_total']))
            ->when(isset($f['max_total']) && $f['max_total'] !== '', fn ($q) => $q->where('total_amount', '<=', (float) $f['max_total']))

            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): SalesReturn
    {
        return SalesReturn::with(['items', 'order', 'customer'])
            ->where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data): SalesReturn
    {
        return SalesReturn::create($data);
    }

    public function update(SalesReturn $return, array $data): SalesReturn
    {
        $return->update($data);

        return $return->refresh();
    }

    public function delete(SalesReturn $return): bool
    {
        return (bool) $return->delete();
    }
}
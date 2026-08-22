<?php

declare(strict_types=1);

namespace App\Modules\Order\Repositories;

use App\Modules\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for order data access operations.
 *
 * @package App\Modules\Order\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class OrderRepository
{
    /**
     * Retrieve a paginated list of orders with optional filters.
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

        return Order::query()
            ->with(['items', 'customer', 'histories'])

            // Global search across order number and customer details
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })

            // Column Filters
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['payment_status']), fn ($q) => $q->where('payment_status', $f['payment_status']))
            ->when(!empty($f['fulfillment_status']), fn ($q) => $q->where('fulfillment_status', $f['fulfillment_status']))
            ->when(isset($f['customer_id']) && $f['customer_id'] !== '', fn ($q) => $q->where('customer_id', $f['customer_id']))
            ->when(!empty($f['payment_method']), fn ($q) => $q->where('payment_method', $f['payment_method']))
            ->when(!empty($f['coupon_code']), fn ($q) => $q->where('coupon_code', $f['coupon_code']))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('placed_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('placed_at', '<=', $date))
            ->when(isset($f['min_total']) && $f['min_total'] !== '', fn ($q) => $q->where('grand_total', '>=', (float) $f['min_total']))
            ->when(isset($f['max_total']) && $f['max_total'] !== '', fn ($q) => $q->where('grand_total', '<=', (float) $f['max_total']))

            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items', 'histories'])->find($id);
    }

    public function findByUuidOrFail(string $uuid): Order
    {
        return Order::with(['items', 'histories', 'customer'])
            ->where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->refresh();
    }

    public function changeStatus(Order $order, string $fromStatus, string $toStatus, ?int $userId = null, ?string $note = null): Order
    {
        $order->update(['status' => $toStatus]);
        $order->histories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $userId,
            'source' => 'admin',
            'note' => $note,
        ]);

        return $order->refresh();
    }

    public function delete(Order $order): bool
    {
        return (bool) $order->delete();
    }

    public function restore(string $uuid): Order
    {
        $order = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $order->restore();

        return $order->refresh();
    }

    public function paginateForCustomer(int $customerId, array $filters): LengthAwarePaginator
    {
        $f = $filters['filters'] ?? $filters;

        return Order::query()
            ->with(['items', 'histories'])
            ->where('customer_id', $customerId)
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['payment_status']), fn ($q) => $q->where('payment_status', $f['payment_status']))
            ->when(!empty($f['fulfillment_status']), fn ($q) => $q->where('fulfillment_status', $f['fulfillment_status']))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('placed_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('placed_at', '<=', $date))
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findByCustomerUuidOrFail(int $customerId, string $uuid): Order
    {
        return Order::with(['items', 'histories'])
            ->where('customer_id', $customerId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }
}
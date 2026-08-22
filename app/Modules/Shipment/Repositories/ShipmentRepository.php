<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Repositories;

use App\Modules\Shipment\Models\Shipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for shipment data access operations.
 *
 * Handles database interactions related to shipments including paginated
 * listing with filters, retrieval, creation, and updating.
 *
 * @package App\Modules\Shipment\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class ShipmentRepository
{
    /**
     * Retrieve a paginated list of shipments with optional filters.
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

        return Shipment::query()
            ->with(['items', 'order'])

            // Global search across shipment/tracking/order details
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('shipment_number', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%")
                        ->orWhere('carrier', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"));
                });
            })

            // Column Filters
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['carrier']), fn ($q) => $q->where('carrier', 'like', '%' . $f['carrier'] . '%'))
            ->when(!empty($f['service']), fn ($q) => $q->where('service', 'like', '%' . $f['service'] . '%'))
            ->when(isset($f['order_id']) && $f['order_id'] !== '', fn ($q) => $q->where('order_id', $f['order_id']))
            ->when(isset($f['customer_id']) && $f['customer_id'] !== '', fn ($q) => $q->whereHas('order', fn ($o) => $o->where('customer_id', $f['customer_id'])))
            ->when($f['shipped_from'] ?? null, fn ($q, $date) => $q->whereDate('shipped_at', '>=', $date))
            ->when($f['shipped_to'] ?? null, fn ($q, $date) => $q->whereDate('shipped_at', '<=', $date))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))

            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findById(int $id): ?Shipment
    {
        return Shipment::with(['items'])->find($id);
    }

    public function findByUuid(string $uuid): ?Shipment
    {
        return Shipment::with(['items'])->where('uuid', $uuid)->first();
    }

    public function findByUuidOrFail(string $uuid): Shipment
    {
        return Shipment::with(['items', 'order'])
            ->where('uuid', $uuid)->firstOrFail();
    }

    public function findByOrderId(int $orderId, string $uuid): ?Shipment
    {
        return Shipment::with(['items'])
            ->where('order_id', $orderId)
            ->where('uuid', $uuid)
            ->first();
    }

    public function create(array $data): Shipment
    {
        return Shipment::create($data);
    }

    public function update(Shipment $shipment, array $data): Shipment
    {
        $shipment->update($data);

        return $shipment->refresh();
    }

    public function delete(Shipment $shipment): bool
    {
        return (bool) $shipment->delete();
    }
}
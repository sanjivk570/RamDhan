<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Repositories;

use App\Modules\Purchase\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class PurchaseOrderRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return PurchaseOrder::query()
            ->with(['supplier', 'creator', 'items'])
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('po_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when(isset($filters['filters']['supplier']), fn ($q) => $q->where('supplier_id', $filters['filters']['supplier']))
            ->when(isset($filters['filters']['status']) && $filters['filters']['status'] !== '', fn ($q) => $q->where('status', $filters['filters']['status']))
            ->when($filters['filters']['from_date'] ?? null, fn ($q, $date) => $q->whereDate('order_date', '>=', $date))
            ->when($filters['filters']['to_date'] ?? null, fn ($q, $date) => $q->whereDate('order_date', '<=', $date))
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): PurchaseOrder
    {
        return PurchaseOrder::with(['supplier','creator','approver','items','goodsReceipts.items'])
            ->where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data): PurchaseOrder { return PurchaseOrder::create($data); }
    public function update(PurchaseOrder $order, array $data): PurchaseOrder { $order->update($data); return $order->refresh(); }
    public function delete(PurchaseOrder $order): bool { return (bool)$order->delete(); }
}

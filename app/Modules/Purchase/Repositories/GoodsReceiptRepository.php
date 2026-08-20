<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Repositories;

use App\Modules\Purchase\Models\GoodsReceipt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class GoodsReceiptRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return GoodsReceipt::query()->with(['supplier','purchaseOrder','receiver','items'])
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('grn_number', 'like', "%{$search}%")
                        ->orWhere('supplier_document_number', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['filters']['supplier']), fn ($q) => $q->where('supplier_id', $filters['filters']['supplier']))
            ->when(isset($filters['filters']['status']) && $filters['filters']['status'] !== '', fn ($q) => $q->where('status', $filters['filters']['status']))
            ->when($filters['filters']['from_date'] ?? null, fn ($q, $date) => $q->whereDate('receipt_date', '>=', $date))
            ->when($filters['filters']['to_date'] ?? null, fn ($q, $date) => $q->whereDate('receipt_date', '<=', $date))
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): GoodsReceipt
    {
        return GoodsReceipt::with(['supplier','purchaseOrder.items','receiver','items.purchaseOrderItem'])
            ->where('uuid', $uuid)->firstOrFail();
    }
}

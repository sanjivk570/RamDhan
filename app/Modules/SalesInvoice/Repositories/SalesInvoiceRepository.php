<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Repositories;

use App\Modules\SalesInvoice\Models\SalesInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for sales invoice data access operations.
 *
 * Handles database interactions related to sales invoices including
 * paginated listing with filters, retrieval, creation, and updating.
 *
 * @package App\Modules\SalesInvoice\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class SalesInvoiceRepository
{
    /**
     * Retrieve a paginated list of invoices with optional filters.
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

        return SalesInvoice::query()
            ->with(['items', 'order', 'customer'])

            // Global search across invoice/order number and customer details
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn ($c) => $c
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })

            // Column Filters
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(isset($f['order_id']) && $f['order_id'] !== '', fn ($q) => $q->where('order_id', $f['order_id']))
            ->when(isset($f['customer_id']) && $f['customer_id'] !== '', fn ($q) => $q->where('customer_id', $f['customer_id']))
            ->when(!empty($f['currency_code']), fn ($q) => $q->where('currency_code', strtoupper($f['currency_code'])))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('invoice_date', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('invoice_date', '<=', $date))
            ->when($f['due_from'] ?? null, fn ($q, $date) => $q->whereDate('due_date', '>=', $date))
            ->when($f['due_to'] ?? null, fn ($q, $date) => $q->whereDate('due_date', '<=', $date))
            ->when(isset($f['min_total']) && $f['min_total'] !== '', fn ($q) => $q->where('grand_total', '>=', (float) $f['min_total']))
            ->when(isset($f['max_total']) && $f['max_total'] !== '', fn ($q) => $q->where('grand_total', '<=', (float) $f['max_total']))
            ->when(isset($f['due_only']) && (bool) $f['due_only'] === true, fn ($q) => $q->where('due_amount', '>', 0))

            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findById(int $id): ?SalesInvoice
    {
        return SalesInvoice::with(['items'])->find($id);
    }

    public function findByUuidOrFail(string $uuid): SalesInvoice
    {
        return SalesInvoice::with(['items', 'order', 'customer'])
            ->where('uuid', $uuid)->firstOrFail();
    }

    public function findByOrderId(int $orderId): ?SalesInvoice
    {
        return SalesInvoice::with(['items'])->where('order_id', $orderId)->first();
    }

    public function create(array $data): SalesInvoice
    {
        return SalesInvoice::create($data);
    }

    public function update(SalesInvoice $invoice, array $data): SalesInvoice
    {
        $invoice->update($data);

        return $invoice->refresh();
    }

    public function delete(SalesInvoice $invoice): bool
    {
        return (bool) $invoice->delete();
    }
}
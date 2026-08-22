<?php

declare(strict_types=1);

namespace App\Modules\Payment\Repositories;

use App\Modules\Payment\Models\PaymentIntent;
use App\Modules\Payment\Models\PaymentRefund;
use App\Modules\Payment\Models\PaymentTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for payment data access operations.
 *
 * Handles database interactions related to payment intents, transactions,
 * and refunds including paginated listing with filters and retrieval.
 *
 * @package App\Modules\Payment\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
final class PaymentRepository
{
    /**
     * Retrieve a paginated list of payment transactions with filters.
     *
     * Supports global search, nested "filters" (or legacy flat keys),
     * sorting, and pagination.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginateTransactions(array $filters): LengthAwarePaginator
    {
        $f = $filters['filters'] ?? $filters;

        return PaymentTransaction::query()
            ->with(['order'])

            // Global search across transaction references and order number
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('provider_transaction_id', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('failure_reason', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"));
                });
            })

            // Column Filters
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['provider']), fn ($q) => $q->where('provider', $f['provider']))
            ->when(!empty($f['payment_method']), fn ($q) => $q->where('payment_method', $f['payment_method']))
            ->when(!empty($f['transaction_type']), fn ($q) => $q->where('transaction_type', $f['transaction_type']))
            ->when(isset($f['order_id']) && $f['order_id'] !== '', fn ($q) => $q->where('order_id', $f['order_id']))
            ->when(isset($f['customer_id']) && $f['customer_id'] !== '', fn ($q) => $q->whereHas('order', fn ($o) => $o->where('customer_id', $f['customer_id'])))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when(isset($f['min_amount']) && $f['min_amount'] !== '', fn ($q) => $q->where('amount', '>=', (float) $f['min_amount']))
            ->when(isset($f['max_amount']) && $f['max_amount'] !== '', fn ($q) => $q->where('amount', '<=', (float) $f['max_amount']))

            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Retrieve a paginated list of payment intents with filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginateIntents(array $filters): LengthAwarePaginator
    {
        $f = $filters['filters'] ?? $filters;

        return PaymentIntent::query()
            ->with(['order'])
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('provider_reference', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"));
                });
            })
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['provider']), fn ($q) => $q->where('provider', $f['provider']))
            ->when(!empty($f['method']), fn ($q) => $q->where('method', $f['method']))
            ->when(isset($f['order_id']) && $f['order_id'] !== '', fn ($q) => $q->where('order_id', $f['order_id']))
            ->when(isset($f['customer_id']) && $f['customer_id'] !== '', fn ($q) => $q->where('customer_id', $f['customer_id']))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Retrieve a paginated list of payment refunds with filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginateRefunds(array $filters): LengthAwarePaginator
    {
        $f = $filters['filters'] ?? $filters;

        return PaymentRefund::query()
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('provider_refund_id', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%");
                });
            })
            ->when(!empty($f['status']), fn ($q) => $q->where('status', $f['status']))
            ->when(!empty($f['provider']), fn ($q) => $q->where('provider', $f['provider']))
            ->when(isset($f['order_id']) && $f['order_id'] !== '', fn ($q) => $q->where('order_id', $f['order_id']))
            ->when(isset($f['payment_transaction_id']) && $f['payment_transaction_id'] !== '', fn ($q) => $q->where('payment_transaction_id', $f['payment_transaction_id']))
            ->when($f['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($f['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findTransactionByUuid(string $uuid): ?PaymentTransaction
    {
        return PaymentTransaction::with(['order'])->where('uuid', $uuid)->first();
    }

    public function findTransactionByUuidOrFail(string $uuid): PaymentTransaction
    {
        return PaymentTransaction::with(['order'])->where('uuid', $uuid)->firstOrFail();
    }

    public function createTransaction(array $data): PaymentTransaction
    {
        return PaymentTransaction::create($data);
    }
}
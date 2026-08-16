<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Repositories;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Models\SupplierUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class SupplierUserRepository
{
    public function paginate(Supplier $supplier, array $filters): LengthAwarePaginator
    {
        $query = SupplierUser::query()
            ->where('supplier_id', $supplier->id)
            ->with(['supplier', 'roles'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['filters']['status']) && $filters['filters']['status'] !== '', function ($query) use ($filters): void {
                $query->where('is_active', (bool) $filters['filters']['status']);
            })
            ->when(!empty($filters['filters']['role']), function ($query) use ($filters): void {
                $role = $filters['filters']['role'];
                $query->whereHas('roles', fn ($q) => $q->where('name', $role));
            })
            ->when(!empty($filters['filters']['primary']), function ($query) use ($filters): void {
                $query->where('is_primary_supplier_user', (bool) $filters['filters']['primary']);
            })
            ->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_order'] ?? 'desc'
            );

        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function findByUuidOrFail(Supplier $supplier, string $uuid): SupplierUser
    {
        return SupplierUser::query()
            ->where('supplier_id', $supplier->id)
            ->with(['supplier', 'roles'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function findByEmail(string $email): ?SupplierUser
    {
        return SupplierUser::withTrashed()->where('email', $email)->first();
    }

    public function create(Supplier $supplier, array $data): SupplierUser
    {
        $data['supplier_id'] = $supplier->id;
        $data['user_type'] = 'supplier';
        $data['uuid'] ??= (string) Str::uuid();

        return SupplierUser::create($data);
    }

    public function update(SupplierUser $user, array $data): SupplierUser
    {
        $user->update($data);

        return $user->refresh()->load(['supplier', 'roles']);
    }

    public function changeStatus(SupplierUser $user, bool $status): SupplierUser
    {
        $user->update(['is_active' => $status]);

        return $user->refresh()->load(['supplier', 'roles']);
    }

    public function delete(SupplierUser $user): bool
    {
        return (bool) $user->delete();
    }

    public function restore(Supplier $supplier, string $uuid): SupplierUser
    {
        $user = SupplierUser::withTrashed()
            ->where('supplier_id', $supplier->id)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $user->restore();

        return $user->refresh()->load(['supplier', 'roles']);
    }

    public function countActivePrimaryUsers(Supplier $supplier, ?int $ignoreUserId = null): int
    {
        return SupplierUser::query()
            ->where('supplier_id', $supplier->id)
            ->where('is_primary_supplier_user', true)
            ->where('is_active', true)
            ->when($ignoreUserId, fn ($q) => $q->where('id', '!=', $ignoreUserId))
            ->count();
    }
}

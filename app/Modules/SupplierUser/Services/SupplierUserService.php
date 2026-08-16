<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Services;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Models\SupplierUser;
use App\Modules\SupplierUser\Repositories\SupplierUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final class SupplierUserService
{
    public function __construct(
        private readonly SupplierUserRepository $repository,
    ) {
    }

    public function list(Supplier $supplier, array $filters)
    {
        return $this->repository->paginate($supplier, $filters);
    }

    public function details(Supplier $supplier, string $uuid): SupplierUser
    {
        return $this->repository->findByUuidOrFail($supplier, $uuid);
    }

    public function create(Supplier $supplier, array $data): SupplierUser
    {
        return DB::transaction(function () use ($supplier, $data): SupplierUser {
            $role = $data['role'] ?? 'supplier_staff';
            $isPrimary = (bool) ($data['is_primary_supplier_user'] ?? false);

            unset($data['role']);
            $data['is_primary_supplier_user'] = $isPrimary;
            $data['password'] = Hash::make($data['password']);

            if ($isPrimary && $this->repository->countActivePrimaryUsers($supplier) > 0) {
                throw new ConflictHttpException('This supplier already has an active primary user.');
            }

            if (SupplierUser::withTrashed()->where('email', $data['email'])->exists()) {
                throw new ConflictHttpException('The email address is already in use.');
            }

            $user = $this->repository->create($supplier, $data);
            $this->assignRole($user, $role);

            return $user->refresh()->load(['supplier', 'roles']);
        });
    }

    public function update(Supplier $supplier, string $uuid, array $data): SupplierUser
    {
        return DB::transaction(function () use ($supplier, $uuid, $data): SupplierUser {
            $user = $this->repository->findByUuidOrFail($supplier, $uuid);
            $role = $data['role'] ?? null;
            $isPrimary = array_key_exists('is_primary_supplier_user', $data)
                ? (bool) $data['is_primary_supplier_user']
                : $user->is_primary_supplier_user;

            unset($data['role']);

            if (array_key_exists('password', $data) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            if ($isPrimary && !$user->is_primary_supplier_user
                && $this->repository->countActivePrimaryUsers($supplier, $user->id) > 0) {
                throw new ConflictHttpException('This supplier already has an active primary user.');
            }

            if ($user->is_primary_supplier_user && !$isPrimary) {
                throw new ConflictHttpException('The primary supplier user cannot be removed as primary. Promote another user first.');
            }

            if (isset($data['email']) && strtolower($data['email']) !== strtolower($user->email)) {
                if (SupplierUser::withTrashed()->where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
                    throw new ConflictHttpException('The email address is already in use.');
                }
            }

            $user = $this->repository->update($user, $data);

            if ($role !== null) {
                $this->assignRole($user, $role);
            }

            return $user->refresh()->load(['supplier', 'roles']);
        });
    }

    public function changeStatus(Supplier $supplier, string $uuid, bool $status): SupplierUser
    {
        $user = $this->repository->findByUuidOrFail($supplier, $uuid);

        if (!$status && $user->is_primary_supplier_user) {
            throw new ConflictHttpException('The primary supplier user cannot be deactivated. Promote another user first.');
        }

        return $this->repository->changeStatus($user, $status);
    }

    public function delete(Supplier $supplier, string $uuid): void
    {
        $user = $this->repository->findByUuidOrFail($supplier, $uuid);

        if ($user->is_primary_supplier_user) {
            throw new ConflictHttpException('The primary supplier user cannot be deleted. Promote another user first.');
        }

        DB::transaction(function () use ($user): void {
            $user->tokens()->delete();
            $this->repository->delete($user);
        });
    }

    public function restore(Supplier $supplier, string $uuid): SupplierUser
    {
        return $this->repository->restore($supplier, $uuid);
    }

    private function assignRole(SupplierUser $user, string $roleName): void
    {
        $allowed = [
            'supplier_owner',
            'supplier_purchase_manager',
            'supplier_accounts',
            'supplier_staff',
        ];

        if (!in_array($roleName, $allowed, true) || !Role::where('name', $roleName)->exists()) {
            throw new ConflictHttpException('Invalid supplier role.');
        }

        $user->syncRoles([$roleName]);
    }
}

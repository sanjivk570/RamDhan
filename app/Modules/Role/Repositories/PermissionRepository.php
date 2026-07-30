<?php

declare(strict_types=1);

namespace App\Modules\Role\Repositories;

use App\Modules\Role\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository for permission data access.
 *
 * Provides methods for retrieving and querying
 * permission records from the database.
 *
 * @package App\Modules\Role\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
class PermissionRepository
{
    /**
     * Retrieve a paginated list of permissions.
     *
     * Applies optional search, sorting, and pagination filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Permission::query()

            ->when(
                $filters['search'] ?? null,
                fn ($query, $search) =>
                    $query->where('name', 'like', "%{$search}%")
            )

            ->orderBy(
                $filters['sort_by'] ?? 'name',
                $filters['sort_order'] ?? 'asc'
            )

            ->paginate(
                $filters['per_page'] ?? 100
            );
    }

    /**
     * Retrieve a permission by its identifier.
     *
     * Throws an exception if the permission does not exist.
     *
     * @param int $id The permission identifier.
     * @return Permission
     */
    public function findByUuidOrFail(int $id): Permission
    {
        return Permission::where('id', $id)->firstOrFail();
    }
}
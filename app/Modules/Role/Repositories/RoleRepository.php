<?php

declare(strict_types=1);

namespace App\Modules\Role\Repositories;

use App\Modules\Role\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository for role data access.
 *
 * Provides methods for retrieving, creating, updating,
 * and deleting role records from the database.
 *
 * @package App\Modules\Role\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
class RoleRepository
{
    /**
     * Retrieve a paginated list of roles.
     *
     * Applies optional search, sorting, and pagination filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Role::query()

            ->when(
                $filters['search'] ?? null,
                fn ($query, $search) =>
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('display_name', 'like', "%{$search}%")
            )

            ->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_order'] ?? 'desc'
            )

            ->paginate(
                $filters['per_page'] ?? 20
            );
    }

    /**
     * Retrieve a role by its identifier.
     *
     * Throws an exception if the role does not exist.
     *
     * @param int $id The role identifier.
     * @return Role
     */
    public function findByUuidOrFail(int $id): Role
    {
        return Role::where('id', $id)->firstOrFail();
    }

    /**
     * Create a new role.
     *
     * @param array<string, mixed> $data The role data.
     * @return Role
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update the specified role.
     *
     * @param Role $role The role instance.
     * @param array<string, mixed> $data The updated role data.
     * @return Role
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role->refresh();
    }

    /**
     * Delete the specified role.
     *
     * @param Role $role The role instance.
     * @return bool
     */
    public function delete(Role $role): bool
    {
        return (bool) $role->delete();
    }
}

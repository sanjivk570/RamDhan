<?php

declare(strict_types=1);

namespace App\Modules\Role\Services;

use App\Modules\Role\Models\Role;
use App\Modules\Role\Repositories\RoleRepository;

/**
 * Service for role-related business logic.
 *
 * Handles role operations by coordinating
 * requests between the application layer and
 * the role repository.
 *
 * @package App\Modules\Role\Services
 * @author Sanjiv Kumar Kushwaha
 */
class RoleService
{
    /**
     * Create a new service instance.
     *
     * @param RoleRepository $roleRepository The role repository.
     */
    public function __construct(
        private readonly RoleRepository $roleRepository
    ) {
    }

    /**
     * Retrieve a paginated list of roles.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function list(array $filters)
    {
        return $this->roleRepository->paginate($filters);
    }

    /**
     * Retrieve the details of a specific role.
     *
     * @param int $id The role identifier.
     * @return Role
     */
    public function details(int $id): Role
    {
        return $this->roleRepository->findByUuidOrFail($id);
    }

    /**
     * Create a new role.
     *
     * @param array<string, mixed> $data The validated role data.
     * @return Role
     */
    public function create(array $data): Role
    {
        return $this->roleRepository->create($data);
    }

    /**
     * Update the specified role.
     *
     * @param int $id The role identifier.
     * @param array<string, mixed> $data The updated role data.
     * @return Role
     */
    public function update(int $id, array $data): Role
    {
        $role = $this->roleRepository->findByUuidOrFail($id);
        return $this->roleRepository->update($role, $data);
    }

    /**
     * Delete the specified role.
     *
     * @param int $id The role identifier.
     * @return void
     */
    public function delete(int $id): void
    {
        $role = $this->roleRepository->findByUuidOrFail($id);

        $this->roleRepository->delete($role);
    }

    /**
     * Retrieve the permissions assigned to a role.
     *
     * @param int $id The role identifier.
     * @return array<int, string>
     */
    public function permissions(int $id): array
    {
        $role = $this->roleRepository->findByUuidOrFail($id);
        return $role->permissions->pluck('name')->toArray();
    }

    /**
     * Synchronize permissions for the specified role.
     *
     * @param int $id The role identifier.
     * @param array<int, string> $permissions The permission names.
     * @return Role
     */
    public function syncPermissions(int $id, array $permissions): Role
    {
        $role = $this->roleRepository->findByUuidOrFail($id);
        $role->syncPermissions($permissions);
        return $role->refresh();
    }
}

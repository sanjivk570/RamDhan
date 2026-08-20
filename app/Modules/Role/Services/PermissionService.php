<?php

declare(strict_types=1);

namespace App\Modules\Role\Services;

use App\Modules\Role\Models\Permission;
use App\Modules\Role\Repositories\PermissionRepository;

/**
 * Service for permission-related business logic.
 *
 * Handles permission operations by delegating
 * data access to the permission repository.
 *
 * @package App\Modules\Role\Services
 * @author Sanjiv Kumar Kushwaha
 */
class PermissionService
{
    /**
     * Create a new service instance.
     *
     * @param PermissionRepository $permissionRepository The permission repository.
     */
    public function __construct(
        private readonly PermissionRepository $permissionRepository
    ) {
    }

    /**
     * Retrieve a paginated list of permissions.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function list(array $filters)
    {
        return $this->permissionRepository->paginate($filters);
    }

    /**
     * Retrieve the details of a specific permission.
     *
     * @param int $id The permission identifier.
     * @return Permission
     */
    public function details(int $id): Permission
    {
        return $this->permissionRepository->findByUuidOrFail($id);
    }
}
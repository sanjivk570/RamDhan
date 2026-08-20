<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Models\Role;
use App\Modules\Role\Services\RoleService;

/**
 * Handle the role permission synchronization action.
 *
 * This action delegates the process of synchronizing
 * permissions assigned to a specific role to the
 * role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class SyncRolePermissionsAction
{
    /**
     * Create a new action instance.
     *
     * @param RoleService $roleService The role service.
     */
    public function __construct(
        private readonly RoleService $roleService
    ) {
    }

    /**
     * Execute the role permission synchronization action.
     *
     * Synchronizes the specified permissions with the given role.
     *
     * @param int $id The role identifier.
     * @param array<int, string> $permissions The list of permission names or identifiers.
     * @return Role
     */
    public function execute(
        int $id,
        array $permissions
    ): Role {
        return $this->roleService->syncPermissions(
            $id,
            $permissions
        );
    }
}
<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Services\RoleService;

/**
 * Handle the retrieval of role permissions.
 *
 * This action delegates the process of fetching
 * permissions assigned to a specific role to the
 * role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class GetRolePermissionsAction
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
     * Execute the get role permissions action.
     *
     * Retrieves all permissions assigned to the specified role.
     *
     * @param int $id The role identifier.
     * @return array<string, mixed>
     */
    public function execute(int $id): array
    {
        return $this->roleService->permissions($id);
    }
}

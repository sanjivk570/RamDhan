<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Models\Role;
use App\Modules\Role\Services\RoleService;

/**
 * Handle the role retrieval action.
 *
 * This action delegates the process of retrieving
 * the details of a specific role to the
 * role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ShowRoleAction
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
     * Execute the role retrieval action.
     *
     * Retrieves the details of the specified role.
     *
     * @param int $id The role identifier.
     * @return Role
     */
    public function execute(int $id): Role
    {
        return $this->roleService->details($id);
    }
}
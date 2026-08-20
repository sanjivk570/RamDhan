<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Services\RoleService;

/**
 * Handle the role deletion action.
 *
 * This action delegates the role deletion process
 * to the role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class DeleteRoleAction
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
     * Execute the role deletion action.
     *
     * Deletes the specified role.
     *
     * @param int $id The role identifier.
     * @return void
     */
    public function execute(int $id): void
    {
        $this->roleService->delete($id);
    }
}

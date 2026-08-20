<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Models\Role;
use App\Modules\Role\Services\RoleService;

/**
 * Handle the role update action.
 *
 * This action delegates the role update process
 * to the role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class UpdateRoleAction
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
     * Execute the role update action.
     *
     * Updates the specified role using the validated data.
     *
     * @param int $id The role identifier.
     * @param array<string, mixed> $data The validated role data.
     * @return Role
     */
    public function execute(int $id, array $data): Role
    {
        return $this->roleService->update($id, $data);
    }
}

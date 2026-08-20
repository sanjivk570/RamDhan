<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Models\Role;
use App\Modules\Role\Services\RoleService;

/**
 * Handle the role creation action.
 *
 * This action delegates the role creation process
 * to the role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class CreateRoleAction
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
     * Execute the role creation action.
     *
     * Creates a new role using the validated data.
     *
     * @param array<string, mixed> $data The validated role data.
     * @return Role
     */
    public function execute(array $data): Role
    {
        return $this->roleService->create($data);
    }
}

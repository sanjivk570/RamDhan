<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Models\Permission;
use App\Modules\Role\Services\PermissionService;

/**
 * Handle the permission retrieval action.
 *
 * This action delegates the process of retrieving
 * the details of a specific permission to the
 * permission service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ShowPermissionAction
{
    /**
     * Create a new action instance.
     *
     * @param PermissionService $permissionService The permission service.
     */
    public function __construct(
        private readonly PermissionService $permissionService
    ) {
    }

    /**
     * Create a new action instance.
     *
     * @param PermissionService $permissionService The permission service.
     */
    public function execute(int $id): Permission
    {
        return $this->permissionService->details($id);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Services\PermissionService;

/**
 * Handle the permission listing action.
 *
 * This action delegates the process of retrieving
 * permissions based on the provided filters to the
 * permission service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ListPermissionAction
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
     * Execute the permission listing action.
     *
     * Retrieves a list of permissions based on the supplied filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return mixed
     */
    public function execute(array $filters)
    {
        return $this->permissionService->list($filters);
    }
}
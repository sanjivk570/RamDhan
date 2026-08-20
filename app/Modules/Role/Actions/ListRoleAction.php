<?php

declare(strict_types=1);

namespace App\Modules\Role\Actions;

use App\Modules\Role\Services\RoleService;

/**
 * Handle the role listing action.
 *
 * This action delegates the process of retrieving
 * roles based on the provided filters to the
 * role service.
 *
 * @package App\Modules\Role\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ListRoleAction
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
     * Execute the role listing action.
     *
     * Retrieves a list of roles based on the supplied filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return mixed
     */
    public function execute(array $filters)
    {
        return $this->roleService->list($filters);
    }
}

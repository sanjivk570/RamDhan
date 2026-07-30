<?php

declare(strict_types=1);

namespace App\Modules\Role\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Role\Actions\ListRoleAction;
use App\Modules\Role\Actions\ShowRoleAction;
use App\Modules\Role\Actions\CreateRoleAction;
use App\Modules\Role\Actions\UpdateRoleAction;
use App\Modules\Role\Actions\DeleteRoleAction;
use App\Modules\Role\Actions\GetRolePermissionsAction;
use App\Modules\Role\Actions\SyncRolePermissionsAction;
use App\Modules\Role\Requests\ListRoleRequest;
use App\Modules\Role\Requests\CreateRoleRequest;
use App\Modules\Role\Requests\UpdateRoleRequest;
use App\Modules\Role\Requests\SyncRolePermissionRequest;
use App\Modules\Role\Resources\RoleResource;

/**
 * Handle role-related API requests.
 *
 * Provides endpoints for managing roles, including
 * listing, creating, updating, deleting, viewing,
 * and synchronizing role permissions.
 *
 * @package App\Modules\Role\Controllers
 * @author Sanjiv Kumar Kushwaha
 */
class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ListRoleAction $listRoleAction The role listing action.
     * @param ShowRoleAction $showRoleAction The role retrieval action.
     * @param CreateRoleAction $createRoleAction The role creation action.
     * @param UpdateRoleAction $updateRoleAction The role update action.
     * @param DeleteRoleAction $deleteRoleAction The role deletion action.
     * @param GetRolePermissionsAction $getRolePermissionsAction The role permission retrieval action.
     * @param SyncRolePermissionsAction $syncRolePermissionsAction The role permission synchronization action.
     */
    public function __construct(
        private readonly ListRoleAction $listRoleAction,
        private readonly ShowRoleAction $showRoleAction,
        private readonly CreateRoleAction $createRoleAction,
        private readonly UpdateRoleAction $updateRoleAction,
        private readonly DeleteRoleAction $deleteRoleAction,
        private readonly GetRolePermissionsAction $getRolePermissionsAction,
        private readonly SyncRolePermissionsAction $syncRolePermissionsAction,
    ) {
    }

    /**
     * Display a listing of roles.
     *
     * Retrieves roles based on the supplied filter criteria.
     *
     * @param ListRoleRequest $request The validated request instance.
     * @return JsonResponse
     */
    public function index(ListRoleRequest $request)
    {
        $roles = $this->listRoleAction->execute(
            $request->validated()
        );

        return ApiResponse::success(
            RoleResource::collection($roles)
        );
    }

    /**
     * Display the specified role.
     *
     * Retrieves the details of a role by its identifier.
     *
     * @param int $id The role identifier.
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $role = $this->showRoleAction->execute($id);

        return ApiResponse::success(
            new RoleResource($role)
        );
    }

    /**
     * Store a newly created role.
     *
     * Creates a new role using the validated request data.
     *
     * @param CreateRoleRequest $request The validated request instance.
     * @return JsonResponse
     */
    public function store(CreateRoleRequest $request)
    {
        $role = $this->createRoleAction->execute(
            $request->validated()
        );

        return ApiResponse::success(
            new RoleResource($role),
            'Role created successfully.'
        );
    }

    /**
     * Update the specified role.
     *
     * Updates the role with the provided validated data.
     *
     * @param UpdateRoleRequest $request The validated request instance.
     * @param int $id The role identifier.
     * @return JsonResponse
     */
    public function update(
        UpdateRoleRequest $request,
        int $id
    ) {
        $role = $this->updateRoleAction->execute(
            $id,
            $request->validated()
        );

        return ApiResponse::success(
            new RoleResource($role),
            'Role updated successfully.'
        );
    }

    /**
     * Remove the specified role.
     *
     * Deletes the specified role.
     *
     * @param int $id The role identifier.
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $this->deleteRoleAction->execute($id);

        return ApiResponse::success(
            [],
            'Role deleted successfully.'
        );
    }

    /**
     * Retrieve permissions assigned to the specified role.
     *
     * @param int $id The role identifier.
     * @return JsonResponse
     */
    public function permissions(int $id)
    {
        return ApiResponse::success(
            $this->getRolePermissionsAction->execute($id)
        );
    }

    /**
     * Synchronize permissions for the specified role.
     *
     * Updates the permissions assigned to the role.
     *
     * @param SyncRolePermissionRequest $request The validated request instance.
     * @param int $id The role identifier.
     * @return JsonResponse
     */
    public function syncPermissions(
        SyncRolePermissionRequest $request,
        int $id
    ) {
        $role = $this->syncRolePermissionsAction->execute(
            $id,
            $request->validated()['permissions']
        );

        return ApiResponse::success(
            new RoleResource($role),
            'Permissions updated successfully.'
        );
    }
}
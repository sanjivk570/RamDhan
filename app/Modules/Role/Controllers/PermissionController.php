<?php

declare(strict_types=1);

namespace App\Modules\Role\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Role\Actions\ListPermissionAction;
use App\Modules\Role\Actions\ShowPermissionAction;
use App\Modules\Role\Requests\ListPermissionRequest;
use App\Modules\Role\Resources\PermissionResource;

/**
 * Handle permission-related API requests.
 *
 * Provides endpoints for listing permissions and
 * retrieving the details of a specific permission.
 *
 * @package App\Modules\Role\Controllers
 * @author Sanjiv Kumar Kushwaha
 */
class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ListPermissionAction $listPermissionAction The permission listing action.
     * @param ShowPermissionAction $showPermissionAction The permission retrieval action.
     */
    public function __construct(
        private readonly ListPermissionAction $listPermissionAction,
        private readonly ShowPermissionAction $showPermissionAction,
    ) {
    }

    /**
     * Display a listing of permissions.
     *
     * Retrieves permissions based on the supplied filter criteria.
     *
     * @param ListPermissionRequest $request The validated request instance.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListPermissionRequest $request)
    {
        $permissions = $this->listPermissionAction->execute(
            $request->validated()
        );

        // return ApiResponse::success(
        //     PermissionResource::collection($permissions)
        // );

        return ApiResponse::paginated(
            $permissions,
            PermissionResource::collection($permissions),
            'Permissions fetched successfully.'
        );
    }

    /**
     * Display the specified permission.
     *
     * Retrieves the details of a permission by its identifier.
     *
     * @param int $id The permission identifier.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $permission = $this->showPermissionAction->execute($id);

        return ApiResponse::success(
            new PermissionResource($permission)
        );
    }
}
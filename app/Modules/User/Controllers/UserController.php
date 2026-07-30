<?php

declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\User\Actions\ListUserAction;
use App\Modules\User\Requests\UserListRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Actions\ShowUserAction;
use App\Modules\User\Requests\CreateUserRequest;
use App\Modules\User\Actions\CreateUserAction;
use App\Modules\User\Actions\UpdateUserAction;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Actions\DeleteUserAction;
use App\Modules\User\Actions\RestoreUserAction;
use App\Modules\User\Actions\ChangeStatusAction;
use App\Modules\User\Requests\ChangeStatusRequest;

/**
 * Controller responsible for user management operations.
 *
 * Handles user listing, retrieval, creation, updating,
 * status changes, deletion, and restoration.
 *
 * @package App\Modules\User\Controllers
 * @author Sanjiv Kumar Kushwaha
 */
class UserController extends Controller
{
    public function __construct(
        private readonly ListUserAction $listUserAction,
        private readonly ShowUserAction $showUserAction,
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly RestoreUserAction $restoreUserAction,
        private readonly ChangeStatusAction $changeStatusAction
    ) {
    }

    /**
     * Create a new controller instance.
     *
     * @param ListUserAction $listUserAction The action for listing users.
     * @param ShowUserAction $showUserAction The action for retrieving a user.
     * @param CreateUserAction $createUserAction The action for creating a user.
     * @param UpdateUserAction $updateUserAction The action for updating a user.
     * @param DeleteUserAction $deleteUserAction The action for deleting a user.
     * @param RestoreUserAction $restoreUserAction The action for restoring a user.
     * @param ChangeStatusAction $changeStatusAction The action for changing a user's status.
     */
    public function index(UserListRequest $request)
    {
        $users = $this->listUserAction->execute(
            $request->validated()
        );

        return ApiResponse::success(
            UserResource::collection($users),
            'Users fetched successfully.'
        );
    }

    /**
     * Display a paginated listing of users.
     *
     * @param UserListRequest $request The validated request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $uuid)
    {
        $user = $this->showUserAction->execute($uuid);
        return ApiResponse::success(
            new UserResource($user),
            'User fetched successfully.'
        );
    }

    /**
     * Display the specified user.
     *
     * @param string $uuid The user UUID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $user = $this->createUserAction->execute(
            $request->validated()
        );

        return ApiResponse::success(
            new UserResource($user),
            'User created successfully.'
        );
    }

    /**
     * Store a newly created user.
     *
     * @param CreateUserRequest $request The validated request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(
        UpdateUserRequest $request,
        string $uuid
    )
    {
        $user = $this->updateUserAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new UserResource($user),
            'User updated successfully.'
        );
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request The validated request.
     * @param string $uuid The user UUID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(
        ChangeStatusRequest $request,
        string $uuid
    )
    {
        $user = $this->changeStatusAction
            ->execute(
                $uuid,
                $request->boolean('status')
            );

        return ApiResponse::success(
            new UserResource($user),
            'User status updated successfully.'
        );
    }

    /**
     * Change the status of the specified user.
     *
     * @param ChangeStatusRequest $request The validated request.
     * @param string $uuid The user UUID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $uuid)
    {
        $this->deleteUserAction
            ->execute($uuid);

        return ApiResponse::success(
            [],
            'User deleted successfully.'
        );
    }

    /**
     * Soft delete the specified user.
     *
     * @param string $uuid The user UUID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(string $uuid)
    {
        $user = $this->restoreUserAction
            ->execute($uuid);

        return ApiResponse::success(
            new UserResource($user),
            'User restored successfully.'
        );
    }

}
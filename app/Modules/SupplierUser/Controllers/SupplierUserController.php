<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Actions\ChangeSupplierUserStatusAction;
use App\Modules\SupplierUser\Actions\CreateSupplierUserAction;
use App\Modules\SupplierUser\Actions\DeleteSupplierUserAction;
use App\Modules\SupplierUser\Actions\ListSupplierUserAction;
use App\Modules\SupplierUser\Actions\RestoreSupplierUserAction;
use App\Modules\SupplierUser\Actions\ShowSupplierUserAction;
use App\Modules\SupplierUser\Actions\UpdateSupplierUserAction;
use App\Modules\SupplierUser\Requests\ChangeSupplierUserStatusRequest;
use App\Modules\SupplierUser\Requests\CreateSupplierUserRequest;
use App\Modules\SupplierUser\Requests\SupplierUserListRequest;
use App\Modules\SupplierUser\Requests\UpdateSupplierUserRequest;
use App\Modules\SupplierUser\Resources\SupplierUserResource;

final class SupplierUserController extends Controller
{
    public function __construct(
        private readonly ListSupplierUserAction $listAction,
        private readonly ShowSupplierUserAction $showAction,
        private readonly CreateSupplierUserAction $createAction,
        private readonly UpdateSupplierUserAction $updateAction,
        private readonly ChangeSupplierUserStatusAction $statusAction,
        private readonly DeleteSupplierUserAction $deleteAction,
        private readonly RestoreSupplierUserAction $restoreAction,
    ) {}

    public function index(SupplierUserListRequest $request, Supplier $supplier)
    {
        $users = $this->listAction->execute($supplier, $request->validated());

        return ApiResponse::paginated(
            $users,
            SupplierUserResource::collection($users),
            'Supplier users fetched successfully.'
        );
    }

    public function show(Supplier $supplier, string $uuid)
    {
        return ApiResponse::success(
            new SupplierUserResource($this->showAction->execute($supplier, $uuid)),
            'Supplier user fetched successfully.'
        );
    }

    public function store(CreateSupplierUserRequest $request, Supplier $supplier)
    {
        return ApiResponse::success(
            new SupplierUserResource($this->createAction->execute($supplier, $request->validated())),
            'Supplier user created successfully.'
        );
    }

    public function update(UpdateSupplierUserRequest $request, Supplier $supplier, string $uuid)
    {
        return ApiResponse::success(
            new SupplierUserResource($this->updateAction->execute($supplier, $uuid, $request->validated())),
            'Supplier user updated successfully.'
        );
    }

    public function changeStatus(ChangeSupplierUserStatusRequest $request, Supplier $supplier, string $uuid)
    {
        return ApiResponse::success(
            new SupplierUserResource($this->statusAction->execute($supplier, $uuid, $request->boolean('status'))),
            'Supplier user status updated successfully.'
        );
    }

    public function destroy(Supplier $supplier, string $uuid)
    {
        $this->deleteAction->execute($supplier, $uuid);

        return ApiResponse::success([], 'Supplier user deleted successfully.');
    }

    public function restore(Supplier $supplier, string $uuid)
    {
        return ApiResponse::success(
            new SupplierUserResource($this->restoreAction->execute($supplier, $uuid)),
            'Supplier user restored successfully.'
        );
    }
}

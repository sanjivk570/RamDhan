<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Supplier\Actions\ChangeSupplierStatusAction;
use App\Modules\Supplier\Actions\CreateSupplierAction;
use App\Modules\Supplier\Actions\DeleteSupplierAction;
use App\Modules\Supplier\Actions\ListSupplierAction;
use App\Modules\Supplier\Actions\RestoreSupplierAction;
use App\Modules\Supplier\Actions\ShowSupplierAction;
use App\Modules\Supplier\Actions\UpdateSupplierAction;
use App\Modules\Supplier\Requests\ChangeSupplierStatusRequest;
use App\Modules\Supplier\Requests\CreateSupplierRequest;
use App\Modules\Supplier\Requests\SupplierListRequest;
use App\Modules\Supplier\Requests\UpdateSupplierRequest;
use App\Modules\Supplier\Resources\SupplierResource;

class SupplierController extends Controller
{
    public function __construct(
        private readonly ListSupplierAction $listSupplierAction,
        private readonly ShowSupplierAction $showSupplierAction,
        private readonly CreateSupplierAction $createSupplierAction,
        private readonly UpdateSupplierAction $updateSupplierAction,
        private readonly DeleteSupplierAction $deleteSupplierAction,
        private readonly RestoreSupplierAction $restoreSupplierAction,
        private readonly ChangeSupplierStatusAction $changeSupplierStatusAction
    ) {
    }

    /**
     * List suppliers.
     */
    public function index(SupplierListRequest $request)
    {
        $suppliers = $this->listSupplierAction->execute($request->validated());

        return ApiResponse::paginated(
            $suppliers,
            SupplierResource::collection($suppliers),
            "Suppliers fetched successfully."
        );
    }

    /**
     * Show supplier.
     */
    public function show(string $uuid)
    {
        $supplier = $this->showSupplierAction->execute($uuid);

        return ApiResponse::success(
            new SupplierResource($supplier),
            "Supplier fetched successfully."
        );
    }

    /**
     * Create supplier.
     */
    public function store(CreateSupplierRequest $request)
    {
        $supplier = $this->createSupplierAction->execute($request->validated());

        return ApiResponse::success(
            new SupplierResource($supplier),
            "Supplier created successfully."
        );
    }

    /**
     * Update supplier.
     */
    public function update(UpdateSupplierRequest $request, string $uuid)
    {
        $supplier = $this->updateSupplierAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new SupplierResource($supplier),
            "Supplier updated successfully."
        );
    }

    /**
     * Change supplier status.
     */
    public function changeStatus(
        ChangeSupplierStatusRequest $request,
        string $uuid
    ) {
        $supplier = $this->changeSupplierStatusAction->execute(
            $uuid,
            $request->boolean("status")
        );

        return ApiResponse::success(
            new SupplierResource($supplier),
            "Supplier status updated successfully."
        );
    }

    /**
     * Delete supplier.
     */
    public function destroy(string $uuid)
    {
        $this->deleteSupplierAction->execute($uuid);

        return ApiResponse::success([], "Supplier deleted successfully.");
    }

    /**
     * Restore supplier.
     */
    public function restore(string $uuid)
    {
        $supplier = $this->restoreSupplierAction->execute($uuid);

        return ApiResponse::success(
            new SupplierResource($supplier),
            "Supplier restored successfully."
        );
    }
}

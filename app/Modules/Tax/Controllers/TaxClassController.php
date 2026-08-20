<?php

declare(strict_types=1);

namespace App\Modules\Tax\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Tax\Actions\TaxClass\ChangeStatusAction;
use App\Modules\Tax\Actions\TaxClass\CreateTaxClassAction;
use App\Modules\Tax\Actions\TaxClass\DeleteTaxClassAction;
use App\Modules\Tax\Actions\TaxClass\ListTaxClassAction;
use App\Modules\Tax\Actions\TaxClass\RestoreTaxClassAction;
use App\Modules\Tax\Actions\TaxClass\ShowTaxClassAction;
use App\Modules\Tax\Actions\TaxClass\UpdateTaxClassAction;
use App\Modules\Tax\Requests\ChangeStatusRequest;
use App\Modules\Tax\Requests\CreateTaxClassRequest;
use App\Modules\Tax\Requests\TaxClassListRequest;
use App\Modules\Tax\Requests\UpdateTaxClassRequest;
use App\Modules\Tax\Resources\TaxClassResource;

class TaxClassController extends Controller
{
    public function __construct(
        private readonly ListTaxClassAction $listTaxClassAction,
        private readonly ShowTaxClassAction $showTaxClassAction,
        private readonly CreateTaxClassAction $createTaxClassAction,
        private readonly UpdateTaxClassAction $updateTaxClassAction,
        private readonly DeleteTaxClassAction $deleteTaxClassAction,
        private readonly RestoreTaxClassAction $restoreTaxClassAction,
        private readonly ChangeStatusAction $changeStatusAction
    ) {
    }

    public function index(TaxClassListRequest $request)
    {
        $taxClasses = $this->listTaxClassAction->execute($request->validated());

        return ApiResponse::paginated(
            $taxClasses,
            TaxClassResource::collection($taxClasses),
            "Tax classes fetched successfully."
        );
    }

    public function show(string $uuid)
    {
        $taxClass = $this->showTaxClassAction->execute($uuid);

        return ApiResponse::success(
            new TaxClassResource($taxClass),
            "Tax class fetched successfully."
        );
    }

    public function store(CreateTaxClassRequest $request)
    {
        $taxClass = $this->createTaxClassAction->execute($request->validated());

        return ApiResponse::success(
            new TaxClassResource($taxClass),
            "Tax class created successfully."
        );
    }

    public function update(UpdateTaxClassRequest $request, string $uuid)
    {
        $taxClass = $this->updateTaxClassAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new TaxClassResource($taxClass),
            "Tax class updated successfully."
        );
    }

    public function changeStatus(ChangeStatusRequest $request, string $uuid)
    {
        $taxClass = $this->changeStatusAction->execute(
            $uuid,
            $request->boolean("status")
        );

        return ApiResponse::success(
            new TaxClassResource($taxClass),
            "Tax class status updated successfully."
        );
    }

    public function destroy(string $uuid)
    {
        $this->deleteTaxClassAction->execute($uuid);

        return ApiResponse::success([], "Tax class deleted successfully.");
    }

    public function restore(string $uuid)
    {
        $taxClass = $this->restoreTaxClassAction->execute($uuid);

        return ApiResponse::success(
            new TaxClassResource($taxClass),
            "Tax class restored successfully."
        );
    }
}

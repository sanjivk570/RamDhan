<?php

declare(strict_types=1);

namespace App\Modules\Tax\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Tax\Actions\TaxRate\ChangeStatusAction;
use App\Modules\Tax\Actions\TaxRate\CreateTaxRateAction;
use App\Modules\Tax\Actions\TaxRate\DeleteTaxRateAction;
use App\Modules\Tax\Actions\TaxRate\ListTaxRateAction;
use App\Modules\Tax\Actions\TaxRate\RestoreTaxRateAction;
use App\Modules\Tax\Actions\TaxRate\ShowTaxRateAction;
use App\Modules\Tax\Actions\TaxRate\UpdateTaxRateAction;
use App\Modules\Tax\Requests\ChangeStatusRequest;
use App\Modules\Tax\Requests\CreateTaxRateRequest;
use App\Modules\Tax\Requests\TaxRateListRequest;
use App\Modules\Tax\Requests\UpdateTaxRateRequest;
use App\Modules\Tax\Resources\TaxRateResource;

class TaxRateController extends Controller
{
    public function __construct(
        private readonly ListTaxRateAction $listTaxRateAction,
        private readonly ShowTaxRateAction $showTaxRateAction,
        private readonly CreateTaxRateAction $createTaxRateAction,
        private readonly UpdateTaxRateAction $updateTaxRateAction,
        private readonly DeleteTaxRateAction $deleteTaxRateAction,
        private readonly RestoreTaxRateAction $restoreTaxRateAction,
        private readonly ChangeStatusAction $changeStatusAction
    ) {
    }

    public function index(
        TaxRateListRequest $request
    ) {
        $taxRates = $this->listTaxRateAction
            ->execute(
                $request->validated()
            );

        return ApiResponse::paginated(
            $taxRates,
            TaxRateResource::collection(
                $taxRates
            ),
            'Tax rates fetched successfully.'
        );
    }

    public function show(
        string $uuid
    ) {
        $taxRate = $this->showTaxRateAction
            ->execute($uuid);

        return ApiResponse::success(
            new TaxRateResource($taxRate),
            'Tax rate fetched successfully.'
        );
    }

    public function store(
        CreateTaxRateRequest $request
    ) {
        $taxRate = $this->createTaxRateAction
            ->execute(
                $request->validated()
            );

        return ApiResponse::success(
            new TaxRateResource($taxRate),
            'Tax rate created successfully.'
        );
    }

    public function update(
        UpdateTaxRateRequest $request,
        string $uuid
    ) {
        $taxRate = $this->updateTaxRateAction
            ->execute(
                $uuid,
                $request->validated()
            );

        return ApiResponse::success(
            new TaxRateResource($taxRate),
            'Tax rate updated successfully.'
        );
    }

    public function changeStatus(
        ChangeStatusRequest $request,
        string $uuid
    ) {
        $taxRate = $this->changeStatusAction
            ->execute(
                $uuid,
                $request->boolean('status')
            );

        return ApiResponse::success(
            new TaxRateResource($taxRate),
            'Tax rate status updated successfully.'
        );
    }

    public function destroy(
        string $uuid
    ) {
        $this->deleteTaxRateAction
            ->execute($uuid);

        return ApiResponse::success(
            [],
            'Tax rate deleted successfully.'
        );
    }

    public function restore(
        string $uuid
    ) {
        $taxRate = $this->restoreTaxRateAction
            ->execute($uuid);

        return ApiResponse::success(
            new TaxRateResource($taxRate),
            'Tax rate restored successfully.'
        );
    }
}
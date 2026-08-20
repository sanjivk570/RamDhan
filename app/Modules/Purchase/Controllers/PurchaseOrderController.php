<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Purchase\Actions\ListPurchaseOrderAction;
use App\Modules\Purchase\Actions\ShowPurchaseOrderAction;
use App\Modules\Purchase\Actions\CreatePurchaseOrderAction;
use App\Modules\Purchase\Actions\UpdatePurchaseOrderAction;
use App\Modules\Purchase\Actions\SubmitPurchaseOrderAction;
use App\Modules\Purchase\Actions\ApprovePurchaseOrderAction;
use App\Modules\Purchase\Actions\CancelPurchaseOrderAction;
use App\Modules\Purchase\Requests\PurchaseOrderListRequest;
use App\Modules\Purchase\Requests\CreatePurchaseOrderRequest;
use App\Modules\Purchase\Requests\UpdatePurchaseOrderRequest;
use App\Modules\Purchase\Requests\ChangePurchaseOrderStatusRequest;
use App\Modules\Purchase\Resources\PurchaseOrderResource;
use Illuminate\Http\Request;

final class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly ListPurchaseOrderAction $listAction,
        private readonly ShowPurchaseOrderAction $showAction,
        private readonly CreatePurchaseOrderAction $createAction,
        private readonly UpdatePurchaseOrderAction $updateAction,
        private readonly SubmitPurchaseOrderAction $submitAction,
        private readonly ApprovePurchaseOrderAction $approveAction,
        private readonly CancelPurchaseOrderAction $cancelAction
    ) {
    }

    public function index(PurchaseOrderListRequest $request)
    {
        $rows = $this->listAction->execute($request->validated());
        return ApiResponse::paginated(
            $rows,
            PurchaseOrderResource::collection($rows),
            "Purchase orders fetched successfully."
        );
    }
    public function show(string $uuid)
    {
        return ApiResponse::success(
            new PurchaseOrderResource($this->showAction->execute($uuid)),
            "Purchase order fetched successfully."
        );
    }
    public function store(CreatePurchaseOrderRequest $request)
    {
        return ApiResponse::success(
            new PurchaseOrderResource(
                $this->createAction->execute(
                    $request->validated(),
                    $request->user()?->id
                )
            ),
            "Purchase order created successfully."
        );
    }
    public function update(UpdatePurchaseOrderRequest $request, string $uuid)
    {
        return ApiResponse::success(
            new PurchaseOrderResource(
                $this->updateAction->execute($uuid, $request->validated())
            ),
            "Purchase order updated successfully."
        );
    }
    public function submit(string $uuid)
    {
        return ApiResponse::success(
            new PurchaseOrderResource($this->submitAction->execute($uuid)),
            "Purchase order submitted successfully."
        );
    }
    public function approve(Request $request, string $uuid)
    {
        return ApiResponse::success(
            new PurchaseOrderResource(
                $this->approveAction->execute($uuid, $request->user()?->id)
            ),
            "Purchase order approved successfully."
        );
    }
    public function cancel(
        ChangePurchaseOrderStatusRequest $request,
        string $uuid
    ) {
        return ApiResponse::success(
            new PurchaseOrderResource(
                $this->cancelAction->execute(
                    $uuid,
                    $request->user()?->id,
                    $request->validated()["reason"] ?? null
                )
            ),
            "Purchase order cancelled successfully."
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Shipment\Actions\AdminListShipmentsAction;
use App\Modules\Shipment\Actions\AdminShowShipmentAction;
use App\Modules\Shipment\Actions\CreateShipmentAction;
use App\Modules\Shipment\Actions\ShipShipmentAction;
use App\Modules\Shipment\Actions\UpdateShipmentAction;
use App\Modules\Shipment\Requests\CreateShipmentRequest;
use App\Modules\Shipment\Requests\ShipmentListRequest;
use App\Modules\Shipment\Requests\UpdateShipmentRequest;
use App\Modules\Shipment\Resources\ShipmentResource;
use Illuminate\Http\Request;

/** Administrative shipment management endpoints. */
final class ShipmentController extends Controller
{
    public function __construct(
        private readonly AdminListShipmentsAction $listAction,
        private readonly AdminShowShipmentAction $showAction,
        private readonly CreateShipmentAction $createAction,
        private readonly UpdateShipmentAction $updateAction,
        private readonly ShipShipmentAction $shipAction,
    ) {}

    public function index(ShipmentListRequest $request)
    {
        $shipments = $this->listAction->execute($request->validated());

        return ApiResponse::paginated(
            $shipments,
            ShipmentResource::collection($shipments),
            'Shipments fetched successfully.'
        );
    }

    public function show(string $uuid)
    {
        return ApiResponse::success(
            new ShipmentResource($this->showAction->execute($uuid)),
            'Shipment fetched successfully.'
        );
    }

    public function store(CreateShipmentRequest $request)
    {
        return ApiResponse::success(
            new ShipmentResource($this->createAction->execute($request->validated(), $request->user()->id)),
            'Shipment created successfully.'
        );
    }

    public function update(UpdateShipmentRequest $request, string $uuid)
    {
        $shipment = $this->showAction->execute($uuid);

        return ApiResponse::success(
            new ShipmentResource($this->updateAction->execute($shipment, $request->validated())),
            'Shipment updated successfully.'
        );
    }

    public function ship(string $uuid)
    {
        return ApiResponse::success(
            new ShipmentResource($this->shipAction->execute($this->showAction->execute($uuid))),
            'Shipment posted successfully.'
        );
    }
}

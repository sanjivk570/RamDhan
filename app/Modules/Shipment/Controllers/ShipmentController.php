<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Shipment\Actions\ListCustomerShipmentsAction;
use App\Modules\Shipment\Actions\ShowCustomerShipmentAction;
use App\Modules\Shipment\Resources\ShipmentResource;
use Illuminate\Http\Request;

/** Customer shipment tracking endpoints. */
final class ShipmentController extends Controller
{
    public function __construct(
        private readonly ListCustomerShipmentsAction $listAction,
        private readonly ShowCustomerShipmentAction $showAction,
    ) {}

    public function index(Request $request)
    {
        return ApiResponse::success(
            ShipmentResource::collection($this->listAction->execute($request->user()->id)),
            'Shipments fetched successfully.'
        );
    }

    public function show(Request $request, string $uuid)
    {
        return ApiResponse::success(
            new ShipmentResource($this->showAction->execute($request->user()->id, $uuid)),
            'Shipment fetched successfully.'
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Shipping\Requests\Admin\ShippingZoneRequest;
use App\Modules\Shipping\Services\ShippingService;

class ShippingZoneController extends Controller
{
    public function __construct(
        private readonly ShippingService $shippingService
    ) {
    }

    public function index()
    {
        $zones = $this->shippingService->listZones(request()->all());

        return ApiResponse::paginated(
            $zones,
            $zones->items(),
            "Shipping zones fetched successfully."
        );
    }

    public function store(ShippingZoneRequest $request)
    {
        $zone = $this->shippingService->createZone($request->validated());

        return ApiResponse::success(
            $zone,
            "Shipping zone created successfully."
        );
    }

    public function update(ShippingZoneRequest $request, string $uuid)
    {
        $zone = $this->shippingService->updateZone(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            $zone,
            "Shipping zone updated successfully."
        );
    }

    public function destroy(string $uuid)
    {
        $this->shippingService->deleteZone($uuid);

        return ApiResponse::success([], "Shipping zone deleted successfully.");
    }
}

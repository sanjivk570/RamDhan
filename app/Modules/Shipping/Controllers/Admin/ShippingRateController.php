<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Shipping\Requests\Admin\ShippingRateRequest;
use App\Modules\Shipping\Services\ShippingService;

class ShippingRateController extends Controller
{
    public function __construct(
        private readonly ShippingService $shippingService
    ) {
    }

    public function index()
    {
        $rates = $this->shippingService->listRates(request()->all());

        return ApiResponse::paginated(
            $rates,
            $rates->items(),
            "Shipping rates fetched successfully."
        );
    }

    public function store(ShippingRateRequest $request)
    {
        $rate = $this->shippingService->createRate($request->validated());

        return ApiResponse::success(
            $rate->load(["zone", "method"]),
            "Shipping rate created successfully."
        );
    }

    public function update(ShippingRateRequest $request, string $uuid)
    {
        $rate = $this->shippingService->updateRate(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            $rate->load(["zone", "method"]),
            "Shipping rate updated successfully."
        );
    }

    public function destroy(string $uuid)
    {
        $this->shippingService->deleteRate($uuid);

        return ApiResponse::success([], "Shipping rate deleted successfully.");
    }
}

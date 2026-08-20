<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Shipping\Requests\Admin\ShippingMethodRequest;
use App\Modules\Shipping\Services\ShippingService;

// use App\Modules\Shipping\Actions\ListShippingMethodAction;
// use App\Modules\Shipping\Actions\CreateShippingMethodAction;
// use App\Modules\Shipping\Actions\UpdateShippingMethodAction;
// use App\Modules\Shipping\Actions\DeleteShippingMethodAction;

class ShippingMethodController extends Controller
{
    public function __construct(
        private readonly ShippingService $shippingService,

        // private readonly ListShippingMethodAction $listShippingMethodAction,
        // private readonly CreateShippingMethodAction $createShippingMethodAction,
        // private readonly UpdateShippingMethodAction $updateShippingMethodAction,
        // private readonly DeleteShippingMethodAction $deleteShippingMethodAction
    ) {
    }

    public function index()
    {
        $methods = $this->shippingService->listMethods(request()->all());

        return ApiResponse::paginated(
            $methods,
            $methods->items(),
            "Shipping methods fetched successfully."
        );
    }

    public function store(ShippingMethodRequest $request)
    {
        $method = $this->shippingService->createMethod($request->validated());

        return ApiResponse::success(
            $method,
            "Shipping method created successfully."
        );
    }

    public function update(ShippingMethodRequest $request, string $uuid)
    {
        $method = $this->shippingService->updateMethod(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            $method,
            "Shipping method updated successfully."
        );
    }

    public function destroy(string $uuid)
    {
        $this->shippingService->deleteMethod($uuid);

        return ApiResponse::success(
            [],
            "Shipping method deleted successfully."
        );
    }
}

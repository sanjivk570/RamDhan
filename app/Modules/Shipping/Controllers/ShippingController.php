<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
//use App\Modules\Shipping\Actions\CalculateShippingRatesAction;
//use App\Modules\Shipping\Requests\CalculateShippingRateRequest;

use App\Modules\Shipping\Actions\CalculateCartShippingAction;
use App\Modules\Shipping\Requests\CalculateShippingRatesRequest;

class ShippingController extends Controller
{
    public function __construct(
        //private readonly CalculateShippingRatesAction $calculateShippingRatesAction,
        private readonly CalculateCartShippingAction $calculateCartShippingAction
    ) {
    }

    // public function rates(CalculateShippingRateRequest $request)
    // {
    //     $rates = $this->calculateShippingRatesAction->execute(
    //         $request->validated()
    //     );

    //     return ApiResponse::success(
    //         $rates,
    //         "Shipping rates calculated successfully."
    //     );
    // }

    /**
     * Calculate shipping for customer's cart.
     *
     * POST /api/v1/shipping/rates
     */
    public function rates(
        CalculateShippingRatesRequest $request
    ) {
        $customer = $request->user('customer');

        $result = $this->calculateCartShippingAction->execute(
            $request->string('cart_uuid')->toString(),
            $request->string('customer_address_uuid')->toString(),
            $customer?->id
        );

        return ApiResponse::success(
            $result,
            'Shipping rates calculated successfully.'
        );
    }
}

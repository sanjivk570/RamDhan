<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Cart\Requests\ApplyShippingRequest;
use App\Modules\Cart\Resources\CartSummaryResource;
use App\Modules\Shipping\Actions\ApplyCartShippingAction;
use App\Modules\Shipping\Actions\CalculateCartShippingAction;
use App\Modules\Shipping\Requests\CalculateShippingRatesRequest;

class ShippingController extends Controller
{
    public function __construct(
        private readonly CalculateCartShippingAction $calculateCartShippingAction,
        private readonly ApplyCartShippingAction $applyCartShippingAction
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
     * Calculate shipping rates for the active cart.
     *
     * POST /api/v1/shipping/rates
     *
     * Works for both authenticated customers (saved address UUID) and
     * guests (inline country/state/postal + guest token). The resolved
     * destination is snapshotted on the cart for later steps.
     */
    public function rates(
        CalculateShippingRatesRequest $request
    ) {
        $customer = $request->user('customer');

        $guestToken = $request->header('X-Guest-Token')
            ?: ($request->input('guest_token') ?: null);

        $result = $this->calculateCartShippingAction->execute(
            $request->string('cart_uuid')->toString(),
            $request->input('customer_address_uuid'),
            $customer?->id,
            [
                'country_code' => $request->input('country_code') ?? $request->input('country'),
                'state_code' => $request->input('state_code') ?? $request->input('state'),
                'postal_code' => $request->input('postal_code'),
            ],
            $guestToken,
        );

        return ApiResponse::success(
            $result,
            'Shipping rates calculated successfully.'
        );
    }

    /**
     * Apply a shopper-selected shipping rate to the cart.
     *
     * POST /api/v1/shipping/apply
     *
     * Validates the selection against the destination and live cart
     * amounts server-side, stores it and recalculates all cart prices.
     */
    public function apply(ApplyShippingRequest $request)
    {
        $customer = $request->user('customer');

        $guestToken = $request->header('X-Guest-Token')
            ?: ($request->input('guest_token') ?: null);

        try {
            $cart = $this->applyCartShippingAction->execute(
                $request->string('cart_uuid')->toString(),
                $request->string('shipping_rate_uuid')->toString(),
                $customer?->id,
                $guestToken,
                $request->input('customer_address_uuid'),
                [
                    'country_code' => $request->input('country_code') ?? $request->input('country'),
                    'state_code' => $request->input('state_code') ?? $request->input('state'),
                    'postal_code' => $request->input('postal_code'),
                ],
            );
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), null, 422);
        }

        return ApiResponse::success(
            new CartSummaryResource($cart),
            'Shipping method applied successfully.'
        );
    }
}

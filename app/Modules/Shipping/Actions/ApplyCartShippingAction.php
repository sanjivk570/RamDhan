<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Actions;

use App\Modules\Cart\Models\Cart;
use App\Modules\Shipping\Services\ShippingService;

/**
 * Apply a shopper-selected shipping rate to a cart.
 *
 * Runs after the address has been selected and the available rates have
 * been listed; validates the selection server-side, stores it on the
 * cart and recalculates all cart prices.
 *
 * @package App\Modules\Shipping\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ApplyCartShippingAction
{
    /**
     * Create a new apply cart shipping action.
     *
     * @param ShippingService $shippingService
     */
    public function __construct(
        private readonly ShippingService $shippingService
    ) {
    }

    /**
     * Execute the shipping application.
     *
     * @param string $cartUuid
     * @param string $rateUuid
     * @param int|null $customerId
     * @param string|null $guestToken
     * @param string|null $customerAddressUuid
     * @param array<string, mixed> $inlineAddress
     * @return Cart Recalculated cart with the shipping method applied.
     */
    public function execute(
        string $cartUuid,
        string $rateUuid,
        ?int $customerId = null,
        ?string $guestToken = null,
        ?string $customerAddressUuid = null,
        array $inlineAddress = []
    ): Cart {
        return $this->shippingService->applyShippingToCart(
            $cartUuid,
            $rateUuid,
            $customerId,
            $guestToken,
            $customerAddressUuid,
            $inlineAddress
        );
    }
}
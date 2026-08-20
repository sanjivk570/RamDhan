<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Actions;

use App\Modules\Shipping\Services\ShippingService;

final class CalculateCartShippingAction
{
    public function __construct(
        private readonly ShippingService $shippingService
    ) {
    }

    /**
     * Calculate shipping using the real cart
     * and customer's saved address.
     *
     * @param string $cartUuid
     * @param string $customerAddressUuid
     * @param int|null $customerId
     * @return array<string, mixed>
     */
    public function execute(
        string $cartUuid,
        string $customerAddressUuid,
        ?int $customerId = null
    ): array {
        return $this->shippingService->calculateCartShipping(
            $cartUuid,
            $customerAddressUuid,
            $customerId
        );
    }
}

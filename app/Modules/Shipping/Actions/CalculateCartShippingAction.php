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
     * Calculate shipping using the real cart plus either the customer's
     * saved address or a guest inline destination.
     *
     * @param string $cartUuid
     * @param string|null $customerAddressUuid Saved customer address UUID (guests pass null).
     * @param int|null $customerId Authenticated customer id (null for guests).
     * @param array<string, mixed> $inlineAddress Guest inline destination
     *                                             (country_code/state_code/postal_code).
     * @param string|null $guestToken Guest cart token (X-Guest-Token).
     * @return array<string, mixed>
     */
    public function execute(
        string $cartUuid,
        ?string $customerAddressUuid = null,
        ?int $customerId = null,
        array $inlineAddress = [],
        ?string $guestToken = null
    ): array {
        return $this->shippingService->calculateCartShipping(
            $cartUuid,
            $customerAddressUuid,
            $customerId,
            $inlineAddress,
            $guestToken
        );
    }
}

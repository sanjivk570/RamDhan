<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

/**
 * Application action for ShowGuestOrderAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ShowGuestOrderAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(string $orderNumber, ?string $guestToken): Order
    {
        return $this->service->guestShow($orderNumber, $guestToken);
    }
}

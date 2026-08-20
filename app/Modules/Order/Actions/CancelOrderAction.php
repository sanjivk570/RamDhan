<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

/**
 * Application action for CancelOrderAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class CancelOrderAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(Order $order, ?string $reason): Order
    {
        return $this->service->cancel($order, $reason);
    }
}

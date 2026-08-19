<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Cart\Models\Cart;
use App\Modules\Order\Services\OrderService;

/**
 * Application action for ReorderAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ReorderAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(Order $order, int $customerId): Cart
    {
        return $this->service->reorder($order, $customerId);
    }
}

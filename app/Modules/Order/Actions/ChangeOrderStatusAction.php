<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

/**
 * Application action for ChangeOrderStatusAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ChangeOrderStatusAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(Order $order, string $status, ?string $note, int $userId): Order
    {
        return $this->service->changeStatus($order, $status, $note, $userId);
    }
}

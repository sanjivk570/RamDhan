<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

/**
 * Application action for AdminShowOrderAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminShowOrderAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(string $uuid): Order
    {
        return $this->service->adminShow($uuid);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

final class ShowOrderSummaryAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(int $customerId, string $uuid): array
    {
        return $this->service->customerSummary($customerId, $uuid);
    }
}

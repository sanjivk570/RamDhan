<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Services\OrderService;

final class GuestShowOrderSummaryAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(string $orderNumber, ?string $guestToken): array
    {
        return $this->service->guestSummary($orderNumber, $guestToken);
    }
}

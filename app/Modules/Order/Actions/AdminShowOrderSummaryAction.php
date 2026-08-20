<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

final class AdminShowOrderSummaryAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(string $uuid): array
    {
        return $this->service->adminSummary($uuid);
    }
}

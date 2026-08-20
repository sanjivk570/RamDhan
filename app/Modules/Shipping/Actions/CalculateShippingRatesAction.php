<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Actions;

use App\Modules\Shipping\Services\ShippingService;
use Illuminate\Support\Collection;

final class CalculateShippingRatesAction
{
    public function __construct(
        private readonly ShippingService $shippingService
    ) {
    }

    public function execute(array $data): Collection
    {
        return $this->shippingService->calculateRates($data);
    }
}

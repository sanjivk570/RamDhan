<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Customer\Models\Customer;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;

/**
 * Application action for CheckoutOrderAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class CheckoutOrderAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(array $data, ?Customer $customer): Order
    {
        return $this->service->checkout($data, $customer);
    }
}

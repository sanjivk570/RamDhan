<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Services\OrderService;

/**
 * Application action for ListCustomerOrdersAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ListCustomerOrdersAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(int $customerId, array $filters)
    {
        return $this->service->listForCustomer($customerId, $filters);
    }
}

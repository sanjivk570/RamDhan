<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Actions;

use App\Modules\SalesReturn\Services\SalesReturnService;

/**
 * Application action for ListCustomerReturnsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ListCustomerReturnsAction
{
    public function __construct(private readonly SalesReturnService $service) {}

    public function execute(int $customerId)
    {
        return $this->service->customerList($customerId);
    }
}

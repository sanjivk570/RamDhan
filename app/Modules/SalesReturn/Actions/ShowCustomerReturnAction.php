<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Actions;

use App\Modules\SalesReturn\Models\SalesReturn;
use App\Modules\SalesReturn\Services\SalesReturnService;

/**
 * Application action for ShowCustomerReturnAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ShowCustomerReturnAction
{
    public function __construct(private readonly SalesReturnService $service) {}

    public function execute(int $customerId, string $uuid): SalesReturn
    {
        return $this->service->customerShow($customerId, $uuid);
    }
}

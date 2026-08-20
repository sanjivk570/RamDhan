<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Actions;

use App\Modules\SalesReturn\Models\SalesReturn;
use App\Modules\SalesReturn\Services\SalesReturnService;

/**
 * Application action for CreateSalesReturnAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class CreateSalesReturnAction
{
    public function __construct(private readonly SalesReturnService $service) {}

    public function execute(array $data, int $customerId): SalesReturn
    {
        return $this->service->create($data, $customerId);
    }
}

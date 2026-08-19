<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Actions;

use App\Modules\SalesReturn\Models\SalesReturn;
use App\Modules\SalesReturn\Services\SalesReturnService;

/**
 * Application action for ProcessSalesReturnAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ProcessSalesReturnAction
{
    public function __construct(private readonly SalesReturnService $service) {}

    public function execute(SalesReturn $return, string $action, ?string $note, int $userId): SalesReturn
    {
        return $this->service->process($return, $action, $note, $userId);
    }
}

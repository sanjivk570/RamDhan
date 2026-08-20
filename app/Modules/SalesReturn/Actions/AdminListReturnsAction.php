<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Actions;

use App\Modules\SalesReturn\Services\SalesReturnService;

/**
 * Application action for AdminListReturnsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListReturnsAction
{
    public function __construct(private readonly SalesReturnService $service) {}

    public function execute(array $filters)
    {
        return $this->service->adminList($filters);
    }
}

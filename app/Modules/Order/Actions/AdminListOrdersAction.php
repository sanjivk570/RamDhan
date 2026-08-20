<?php

declare(strict_types=1);

namespace App\Modules\Order\Actions;

use App\Modules\Order\Services\OrderService;

/**
 * Application action for AdminListOrdersAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListOrdersAction
{
    public function __construct(private readonly OrderService $service) {}

    public function execute(array $filters)
    {
        return $this->service->adminList($filters);
    }
}

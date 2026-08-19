<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Actions;

use App\Modules\Shipment\Services\ShipmentService;

/**
 * Application action for AdminListShipmentsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListShipmentsAction
{
    public function __construct(private readonly ShipmentService $service) {}

    public function execute(array $filters)
    {
        return $this->service->listAdmin($filters);
    }
}

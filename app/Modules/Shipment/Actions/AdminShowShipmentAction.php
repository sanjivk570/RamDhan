<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Actions;

use App\Modules\Shipment\Models\Shipment;
use App\Modules\Shipment\Services\ShipmentService;

/**
 * Application action for AdminShowShipmentAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminShowShipmentAction
{
    public function __construct(private readonly ShipmentService $service) {}

    public function execute(string $uuid): Shipment
    {
        return $this->service->showAdmin($uuid);
    }
}

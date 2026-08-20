<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Actions;

use App\Modules\Shipment\Models\Shipment;
use App\Modules\Shipment\Services\ShipmentService;

/**
 * Application action for UpdateShipmentAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class UpdateShipmentAction
{
    public function __construct(private readonly ShipmentService $service) {}

    public function execute(Shipment $shipment, array $data): Shipment
    {
        return $this->service->update($shipment, $data);
    }
}

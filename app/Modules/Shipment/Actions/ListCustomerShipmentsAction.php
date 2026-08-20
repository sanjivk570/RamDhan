<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Actions;

use App\Modules\Shipment\Services\ShipmentService;

/**
 * Application action for ListCustomerShipmentsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ListCustomerShipmentsAction
{
    public function __construct(private readonly ShipmentService $service) {}

    public function execute(int $customerId)
    {
        return $this->service->listForCustomer($customerId);
    }
}

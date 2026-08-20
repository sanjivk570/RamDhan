<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Actions;

use App\Modules\Inventory\Services\InventoryService;

class ListInventoryAction
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    /**
     * Execute inventory listing.
     */
    public function execute(array $filters)
    {
        return $this->inventoryService->list($filters);
    }
}

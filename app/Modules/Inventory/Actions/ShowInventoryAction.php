<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Actions;

use App\Modules\Inventory\Models\InventoryStock;
use App\Modules\Inventory\Services\InventoryService;

class ShowInventoryAction
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function execute(string $uuid): InventoryStock
    {
        return $this->inventoryService->details($uuid);
    }
}

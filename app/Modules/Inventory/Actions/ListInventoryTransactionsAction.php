<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Actions;

use App\Modules\Inventory\Services\InventoryService;

class ListInventoryTransactionsAction
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function execute(string $uuid, int $perPage = 20)
    {
        return $this->inventoryService->transactions($uuid, $perPage);
    }
}

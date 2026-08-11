<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Actions;

use App\Modules\Inventory\Models\InventoryStock;
use App\Modules\Inventory\Services\InventoryService;

class AdjustInventoryAction
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function execute(
        string $uuid,
        float $quantity,
        ?string $notes = null,
        ?int $createdBy = null
    ): InventoryStock {
        return $this->inventoryService->adjust(
            $uuid,
            $quantity,
            $notes,
            $createdBy
        );
    }
}

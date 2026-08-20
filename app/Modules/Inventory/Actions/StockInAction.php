<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Actions;

use App\Modules\Inventory\Models\InventoryStock;
use App\Modules\Inventory\Services\InventoryService;

class StockInAction
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function execute(
        string $uuid,
        float $quantity,
        string $type,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $notes = null,
        ?int $createdBy = null
    ): InventoryStock {
        return $this->inventoryService->stockIn(
            $uuid,
            $quantity,
            $type,
            $referenceType,
            $referenceId,
            $notes,
            $createdBy
        );
    }
}

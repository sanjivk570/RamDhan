<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Repositories;

use App\Modules\Inventory\Models\InventoryStock;
use App\Modules\Inventory\Models\InventoryTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InventoryTransactionRepository
{
    /**
     * Create transaction.
     */
    public function create(array $data): InventoryTransaction
    {
        return InventoryTransaction::create($data);
    }

    /**
     * Get transaction by UUID.
     */
    public function findByUuid(string $uuid): ?InventoryTransaction
    {
        return InventoryTransaction::with([
            "product",
            "inventoryStock",
            "createdBy",
        ])
            ->where("uuid", $uuid)
            ->first();
    }

    /**
     * Get transaction history.
     */
    public function paginate(
        InventoryStock $inventoryStock,
        int $perPage = 20
    ): LengthAwarePaginator {
        return InventoryTransaction::query()
            ->with(["createdBy"])
            ->where("inventory_stock_id", $inventoryStock->id)
            ->latest()
            ->paginate($perPage);
    }
}

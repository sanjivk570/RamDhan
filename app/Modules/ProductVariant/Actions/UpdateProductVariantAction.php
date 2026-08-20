<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Actions;

use App\Modules\ProductVariant\Services\ProductVariantService;
use App\Modules\Inventory\Services\InventoryService;

class UpdateProductVariantAction
{
    public function __construct(
        private readonly ProductVariantService $service,
        private readonly InventoryService $inventoryService
    )
    {
    }

    public function execute(string $productUuid, string $variantUuid, array $data) {
        //return $this->service->update($productUuid, $variantUuid, $data);
        $productVariant = $this->service->update($productUuid, $variantUuid, $data);
        //Update invetory also
        $this->inventoryService->updateInitialStockForProductVariant($productVariant, $data);
        $productVariant->refresh();
        return $productVariant;
    }
}

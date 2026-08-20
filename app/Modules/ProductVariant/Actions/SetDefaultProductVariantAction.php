<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Actions;

use App\Modules\ProductVariant\Services\ProductVariantService;

class SetDefaultProductVariantAction
{
    public function __construct(private readonly ProductVariantService $service)
    {
    }

    public function execute(string $productUuid, string $variantUuid)
    {
        return $this->service->setDefault($productUuid, $variantUuid);
    }
}

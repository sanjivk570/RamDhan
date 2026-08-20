<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Actions;

use App\Modules\ProductVariant\Services\ProductVariantService;

class ListProductVariantAction
{
    public function __construct(private readonly ProductVariantService $service)
    {
    }

    public function execute(string $productUuid, array $filters)
    {
        return $this->service->list($productUuid, $filters);
    }
}

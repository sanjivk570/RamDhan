<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Actions;

use App\Modules\Supplier\Services\SupplierService;

final class ListSupplierAction
{
    public function __construct(
        private readonly SupplierService $supplierService
    ) {
    }

    public function execute(array $filters)
    {
        return $this->supplierService->list($filters);
    }
}

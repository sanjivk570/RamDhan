<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Actions;

use App\Modules\Supplier\Services\SupplierService;

final class DeleteSupplierAction
{
    public function __construct(
        private readonly SupplierService $supplierService
    ) {
    }

    public function execute(string $uuid): void
    {
        $this->supplierService->delete($uuid);
    }
}

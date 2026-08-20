<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Actions;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\Supplier\Services\SupplierService;

final class CreateSupplierAction
{
    public function __construct(
        private readonly SupplierService $supplierService
    ) {
    }

    public function execute(array $data): Supplier
    {
        return $this->supplierService->create($data);
    }
}

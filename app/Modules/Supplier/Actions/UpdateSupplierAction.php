<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Actions;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\Supplier\Services\SupplierService;

final class UpdateSupplierAction
{
    public function __construct(
        private readonly SupplierService $supplierService
    ) {
    }

    public function execute(string $uuid, array $data): Supplier
    {
        return $this->supplierService->update($uuid, $data);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Actions;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\Supplier\Services\SupplierService;

final class ChangeSupplierStatusAction
{
    public function __construct(
        private readonly SupplierService $supplierService
    ) {
    }

    public function execute(string $uuid, bool $status): Supplier
    {
        return $this->supplierService->changeStatus($uuid, $status);
    }
}

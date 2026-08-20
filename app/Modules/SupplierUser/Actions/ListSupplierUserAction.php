<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Actions;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Services\SupplierUserService;

final class ListSupplierUserAction
{
    public function __construct(private readonly SupplierUserService $service) {}

    public function execute(Supplier $supplier, array $filters)
    {
        return $this->service->list($supplier, $filters);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Actions;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Services\SupplierUserService;

final class CreateSupplierUserAction
{
    public function __construct(private readonly SupplierUserService $service) {}

    public function execute(Supplier $supplier, array $data)
    {
        return $this->service->create($supplier, $data);
    }
}

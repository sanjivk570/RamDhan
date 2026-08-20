<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Actions;

use App\Modules\Supplier\Models\Supplier;
use App\Modules\SupplierUser\Services\SupplierUserService;

final class ShowSupplierUserAction
{
    public function __construct(private readonly SupplierUserService $service) {}

    public function execute(Supplier $supplier, string $uuid)
    {
        return $this->service->details($supplier, $uuid);
    }
}

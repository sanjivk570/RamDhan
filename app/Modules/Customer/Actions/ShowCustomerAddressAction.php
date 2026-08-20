<?php

declare(strict_types=1);

namespace App\Modules\Customer\Actions;

use App\Modules\Customer\Models\CustomerAddress;
use App\Modules\Customer\Services\CustomerAddressService;

final class ShowCustomerAddressAction
{
    public function __construct(
        private readonly CustomerAddressService $service
    ) {
    }

    public function execute(int $customerId, string $uuid): CustomerAddress
    {
        return $this->service->details($customerId, $uuid);
    }
}

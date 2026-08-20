<?php

declare(strict_types=1);

namespace App\Modules\Customer\Actions;

use App\Modules\Customer\Models\CustomerAddress;
use App\Modules\Customer\Services\CustomerAddressService;

final class CreateCustomerAddressAction
{
    public function __construct(
        private readonly CustomerAddressService $service
    ) {
    }

    public function execute(int $customerId, array $data): CustomerAddress
    {
        return $this->service->create($customerId, $data);
    }
}

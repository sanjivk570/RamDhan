<?php

declare(strict_types=1);

namespace App\Modules\Customer\Actions;

use App\Modules\Customer\Services\CustomerAddressService;

final class ListCustomerAddressAction
{
    public function __construct(
        private readonly CustomerAddressService $service
    ) {
    }

    public function execute(int $customerId, array $filters)
    {
        return $this->service->list($customerId, $filters);
    }
}

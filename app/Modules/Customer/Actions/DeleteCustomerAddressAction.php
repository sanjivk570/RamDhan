<?php

declare(strict_types=1);

namespace App\Modules\Customer\Actions;

use App\Modules\Customer\Services\CustomerAddressService;

final class DeleteCustomerAddressAction
{
    public function __construct(
        private readonly CustomerAddressService $service
    ) {
    }

    public function execute(int $customerId, string $uuid): void
    {
        $this->service->delete($customerId, $uuid);
    }
}

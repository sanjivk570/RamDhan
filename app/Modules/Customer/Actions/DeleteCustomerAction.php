<?php

declare(strict_types=1);

namespace App\Modules\Customer\Actions;

use App\Modules\Customer\Services\CustomerService;

final class DeleteCustomerAction
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    public function execute(string $uuid): void
    {
        $this->customerService->delete($uuid);
    }
}

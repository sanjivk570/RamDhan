<?php

declare(strict_types=1);

namespace App\Modules\Customer\Actions;

use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Services\CustomerService;

final class CreateCustomerAction
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    public function execute(array $data): Customer
    {
        return $this->customerService->create($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Actions;

use App\Modules\SalesInvoice\Services\SalesInvoiceService;

/**
 * Application action for ListCustomerInvoicesAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ListCustomerInvoicesAction
{
    public function __construct(private readonly SalesInvoiceService $service) {}

    public function execute(int $customerId)
    {
        return $this->service->customerList($customerId);
    }
}

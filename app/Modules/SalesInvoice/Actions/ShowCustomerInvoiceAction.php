<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Actions;

use App\Modules\SalesInvoice\Models\SalesInvoice;
use App\Modules\SalesInvoice\Services\SalesInvoiceService;

/**
 * Application action for ShowCustomerInvoiceAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ShowCustomerInvoiceAction
{
    public function __construct(private readonly SalesInvoiceService $service) {}

    public function execute(int $customerId, string $uuid): SalesInvoice
    {
        return $this->service->customerShow($customerId, $uuid);
    }
}

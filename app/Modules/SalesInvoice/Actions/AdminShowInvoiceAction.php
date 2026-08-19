<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Actions;

use App\Modules\SalesInvoice\Models\SalesInvoice;
use App\Modules\SalesInvoice\Services\SalesInvoiceService;

/**
 * Application action for AdminShowInvoiceAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminShowInvoiceAction
{
    public function __construct(private readonly SalesInvoiceService $service) {}

    public function execute(string $uuid): SalesInvoice
    {
        return $this->service->adminShow($uuid);
    }
}

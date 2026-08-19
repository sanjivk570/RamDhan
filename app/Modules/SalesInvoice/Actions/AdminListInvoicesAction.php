<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Actions;

use App\Modules\SalesInvoice\Services\SalesInvoiceService;

/**
 * Application action for AdminListInvoicesAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListInvoicesAction
{
    public function __construct(private readonly SalesInvoiceService $service) {}

    public function execute(array $filters)
    {
        return $this->service->adminList($filters);
    }
}

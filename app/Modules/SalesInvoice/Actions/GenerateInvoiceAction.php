<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\SalesInvoice\Models\SalesInvoice;
use App\Modules\SalesInvoice\Services\SalesInvoiceService;

/**
 * Application action for GenerateInvoiceAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class GenerateInvoiceAction
{
    public function __construct(private readonly SalesInvoiceService $service) {}

    public function execute(Order $order): SalesInvoice
    {
        return $this->service->createForOrder($order);
    }
}

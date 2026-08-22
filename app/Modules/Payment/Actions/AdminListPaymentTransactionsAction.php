<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Payment\Services\PaymentService;

/**
 * Application action for AdminListPaymentTransactionsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListPaymentTransactionsAction
{
    public function __construct(private readonly PaymentService $service) {}

    public function execute(array $filters)
    {
        return $this->service->listTransactions($filters);
    }
}

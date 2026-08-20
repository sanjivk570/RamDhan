<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Payment\Models\PaymentTransaction;

/**
 * Application action for AdminListPaymentTransactionsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListPaymentTransactionsAction
{
    public function execute(array $filters)
    {
        return PaymentTransaction::query()->latest()->paginate($filters['per_page'] ?? 20);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\PaymentRefund;
use App\Modules\Payment\Services\PaymentService;

/**
 * Application action for RefundPaymentAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class RefundPaymentAction
{
    public function __construct(private readonly PaymentService $service) {}

    public function execute(Order $order, float $amount, string $reason): PaymentRefund
    {
        return $this->service->refund($order, $amount, $reason);
    }
}

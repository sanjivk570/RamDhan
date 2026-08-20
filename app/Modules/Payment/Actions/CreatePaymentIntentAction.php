<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\PaymentIntent;
use App\Modules\Payment\Services\PaymentService;

/**
 * Application action for CreatePaymentIntentAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class CreatePaymentIntentAction
{
    public function __construct(private readonly PaymentService $service) {}

    public function execute(Order $order, string $provider, string $method): PaymentIntent
    {
        return $this->service->createIntent($order, $provider, $method);
    }
}

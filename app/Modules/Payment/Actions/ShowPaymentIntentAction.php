<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Payment\Models\PaymentIntent;
use App\Modules\Payment\Services\PaymentService;

/**
 * Application action for ShowPaymentIntentAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class ShowPaymentIntentAction
{
    public function __construct(private readonly PaymentService $service) {}

    public function execute(?int $customerId, ?string $guestToken, string $uuid): PaymentIntent
    {
        return $this->service->showIntent($customerId, $guestToken, $uuid);
    }
}

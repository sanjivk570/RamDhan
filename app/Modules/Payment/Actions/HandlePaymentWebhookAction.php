<?php

declare(strict_types=1);

namespace App\Modules\Payment\Actions;

use App\Modules\Payment\Services\PaymentService;

/**
 * Application action for HandlePaymentWebhookAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class HandlePaymentWebhookAction
{
    public function __construct(private readonly PaymentService $service) {}

    public function execute(string $provider, string $event, array $payload, array $headers): array
    {
        return $this->service->handleWebhook($provider, $event, $payload, $headers);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Payment\Contracts;
use App\Modules\Order\Models\Order;
interface PaymentGatewayContract
{
    public function createPayment(Order $order, string $method): array;
    public function verifyWebhook(
        string $event,
        array $payload,
        array $headers = []
    ): array;
    public function refund(
        string $providerTransactionId,
        float $amount,
        string $reason = ""
    ): array;
}

<?php

declare(strict_types=1);

namespace App\Modules\Payment\Services;
use App\Modules\Payment\Contracts\PaymentGatewayContract;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Str;
final class DatabasePaymentGateway implements PaymentGatewayContract
{
    public function createPayment(Order $order, string $method): array
    {
        if ($method === "cod") {
            return [
                "status" => "created",
                "provider_reference" => "COD-" . Str::upper(Str::random(16)),
                "request" => ["method" => "cod"],
                "response" => null,
            ];
        }
        return [
            "status" => "created",
            "provider_reference" => "PAY-" . Str::upper(Str::random(16)),
            "request" => [
                "method" => $method,
                "amount" => $order->grand_total,
                "currency" => $order->currency_code,
            ],
            "response" => [
                "message" =>
                    "Gateway adapter placeholder; bind a real provider in PaymentServiceProvider.",
            ],
        ];
    }
    public function verifyWebhook(
        string $event,
        array $payload,
        array $headers = []
    ): array {
        return [
            "status" => $payload["status"] ?? "pending",
            "order_uuid" => $payload["order_uuid"] ?? null,
            "transaction_id" => $payload["transaction_id"] ?? null,
            "amount" => $payload["amount"] ?? null,
            "method" => $payload["method"] ?? null,
            "reference" => $payload["reference"] ?? null,
        ];
    }
    public function refund(
        string $providerTransactionId,
        float $amount,
        string $reason = ""
    ): array {
        return [
            "status" => "pending",
            "refund_id" => "REF-" . Str::upper(Str::random(16)),
            "amount" => $amount,
            "reason" => $reason,
        ];
    }
}

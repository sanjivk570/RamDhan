<?php

declare(strict_types=1);

namespace App\Modules\Payment\Services;
use App\Modules\Payment\Contracts\PaymentGatewayContract;
use App\Modules\Payment\Models\PaymentIntent;
use App\Modules\Payment\Models\PaymentTransaction;
use App\Modules\Payment\Models\PaymentRefund;
use App\Modules\Payment\Repositories\PaymentRepository;
use App\Modules\Order\Models\Order;
use App\Modules\SalesInvoice\Models\SalesInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
final class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayContract $gateway,
        private readonly PaymentRepository $repository
    ) {
    }
    /**
     * Retrieve a paginated list of payment transactions.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTransactions(array $filters)
    {
        return $this->repository->paginateTransactions($filters);
    }

    public function createIntent(
        Order $order,
        string $provider,
        string $method
    ): PaymentIntent {
        return DB::transaction(function () use ($order, $provider, $method) {
            $r = $this->gateway->createPayment($order, $method);
            return PaymentIntent::create([
                "order_id" => $order->id,
                "customer_id" => $order->customer_id,
                "provider" => $provider,
                "method" => $method,
                "status" => $r["status"] ?? "created",
                "provider_reference" => $r["provider_reference"] ?? null,
                "amount" => $order->grand_total,
                "currency_code" => $order->currency_code,
                "provider_payload" => $r["request"] ?? null,
                "provider_response" => $r["response"] ?? null,
                "expires_at" => $r["expires_at"] ?? null,
            ]);
        });
    }
    public function handleWebhook(
        string $provider,
        string $event,
        array $payload,
        array $headers = []
    ): array {
        return DB::transaction(function () use (
            $provider,
            $event,
            $payload,
            $headers
        ) {
            $r = $this->gateway->verifyWebhook($event, $payload, $headers);
            if (!empty($r["order_uuid"])) {
                $order = Order::where("uuid", $r["order_uuid"])
                    ->lockForUpdate()
                    ->first();
                if ($order && ($r["status"] ?? null) === "success") {
                    if (
                        !empty($r["transaction_id"]) &&
                        PaymentTransaction::where("provider", $provider)
                            ->where(
                                "provider_transaction_id",
                                $r["transaction_id"]
                            )
                            ->exists()
                    ) {
                        return $r;
                    }
                    $order->update([
                        "payment_status" => "paid",
                        "status" =>
                            $order->status === "pending"
                                ? Order::CONFIRMED
                                : $order->status,
                    ]);
                    SalesInvoice::where("order_id", $order->id)->update([
                        "paid_amount" => $order->grand_total,
                        "due_amount" => 0,
                        "status" => "paid",
                    ]);
                    PaymentTransaction::create([
                        "order_id" => $order->id,
                        "provider" => $provider,
                        "transaction_type" => "payment",
                        "status" => "success",
                        "provider_transaction_id" =>
                            $r["transaction_id"] ?? null,
                        "amount" => $r["amount"] ?? $order->grand_total,
                        "currency_code" => $order->currency_code,
                        "payment_method" => $r["method"] ?? null,
                        "reference_number" => $r["reference"] ?? null,
                        "payload" => $payload,
                    ]);
                }
            }
            return $r;
        });
    }
    public function showIntent(
        ?int $customerId,
        ?string $guestToken,
        string $uuid
    ): PaymentIntent {
        $q = PaymentIntent::where("uuid", $uuid);
        if ($customerId) {
            $q->where("customer_id", $customerId);
        } else {
            $q->whereHas(
                "order",
                fn($x) => $x->where("guest_token", $guestToken)
            );
        }
        return $q->firstOrFail();
    }
    public function refund(
        Order $order,
        float $amount,
        string $reason
    ): PaymentRefund {
        return DB::transaction(function () use ($order, $amount, $reason) {
            $tx = PaymentTransaction::where("order_id", $order->id)
                ->where("status", "success")
                ->latest()
                ->first();
            $r = $tx
                ? $this->gateway->refund(
                    (string) $tx->provider_transaction_id,
                    $amount,
                    $reason
                )
                : ["status" => "pending"];
            return PaymentRefund::create([
                "order_id" => $order->id,
                "payment_transaction_id" => $tx?->id,
                "provider" => $tx?->provider,
                "status" => $r["status"] ?? "pending",
                "provider_refund_id" => $r["refund_id"] ?? null,
                "amount" => $amount,
                "currency_code" => $order->currency_code,
                "reason" => $reason,
                "payload" => $r,
            ]);
        });
    }
}

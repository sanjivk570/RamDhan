<?php

declare(strict_types=1);

namespace App\Modules\Payment\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class PaymentIntentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "order_id" => $this->order_id,
            "customer_id" => $this->customer_id,
            "provider" => $this->provider,
            "method" => $this->method,
            "status" => $this->status,
            "provider_reference" => $this->provider_reference,
            "amount" => $this->amount,
            "currency_code" => $this->currency_code,
            "provider_payload" => $this->provider_payload,
            "expires_at" => $this->expires_at,
        ];
    }
}

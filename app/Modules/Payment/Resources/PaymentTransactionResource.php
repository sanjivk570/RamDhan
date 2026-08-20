<?php

declare(strict_types=1);

namespace App\Modules\Payment\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class PaymentTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "order_id" => $this->order_id,
            "provider" => $this->provider,
            "transaction_type" => $this->transaction_type,
            "status" => $this->status,
            "provider_transaction_id" => $this->provider_transaction_id,
            "amount" => $this->amount,
            "currency_code" => $this->currency_code,
            "payment_method" => $this->payment_method,
            "reference_number" => $this->reference_number,
            "created_at" => $this->created_at,
        ];
    }
}

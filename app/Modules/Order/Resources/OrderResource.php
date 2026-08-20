<?php

declare(strict_types=1);

namespace App\Modules\Order\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "order_number" => $this->order_number,
            "customer_id" => $this->customer_id,
            "customer_email" => $this->customer_email,
            "customer_name" => $this->customer_name,
            "customer_phone" => $this->customer_phone,
            "status" => $this->status,
            "payment_status" => $this->payment_status,
            "fulfillment_status" => $this->fulfillment_status,
            "currency_code" => $this->currency_code,
            "subtotal" => $this->subtotal,
            "discount_amount" => $this->discount_amount,
            "tax_amount" => $this->tax_amount,
            "shipping_amount" => $this->shipping_amount,
            "grand_total" => $this->grand_total,
            "coupon_code" => $this->coupon_code,
            "payment_method" => $this->payment_method,
            "billing_address" => $this->billing_address,
            "shipping_address" => $this->shipping_address,
            "placed_at" => $this->placed_at,
            "items" => $this->whenLoaded("items"),
            "histories" => $this->whenLoaded("histories"),
        ];
    }
}

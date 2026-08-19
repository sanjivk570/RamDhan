<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class SalesInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "invoice_number" => $this->invoice_number,
            "order_id" => $this->order_id,
            "customer_id" => $this->customer_id,
            "status" => $this->status,
            "invoice_date" => $this->invoice_date,
            "due_date" => $this->due_date,
            "currency_code" => $this->currency_code,
            "subtotal" => $this->subtotal,
            "discount_amount" => $this->discount_amount,
            "tax_amount" => $this->tax_amount,
            "shipping_amount" => $this->shipping_amount,
            "grand_total" => $this->grand_total,
            "paid_amount" => $this->paid_amount,
            "due_amount" => $this->due_amount,
            "billing_address" => $this->billing_address,
            "items" => $this->whenLoaded("items"),
        ];
    }
}

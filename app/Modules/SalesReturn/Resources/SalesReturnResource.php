<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class SalesReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "return_number" => $this->return_number,
            "order_id" => $this->order_id,
            "customer_id" => $this->customer_id,
            "status" => $this->status,
            "refund_status" => $this->refund_status,
            "total_amount" => $this->total_amount,
            "reason" => $this->reason,
            "customer_note" => $this->customer_note,
            "admin_note" => $this->admin_note,
            "approved_at" => $this->approved_at,
            "rejected_at" => $this->rejected_at,
            "items" => $this->whenLoaded("items"),
        ];
    }
}

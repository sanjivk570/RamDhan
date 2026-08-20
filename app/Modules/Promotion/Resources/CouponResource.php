<?php

declare(strict_types=1);
namespace App\Modules\Promotion\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "code" => $this->code,
            "name" => $this->name,
            "discount_type" => $this->discount_type,
            "discount_value" => $this->discount_value,
            "maximum_discount" => $this->maximum_discount,
            "minimum_order_amount" => $this->minimum_order_amount,
            "usage_limit" => $this->usage_limit,
            "per_customer_limit" => $this->per_customer_limit,
            "used_count" => $this->used_count,
            "starts_at" => $this->starts_at,
            "ends_at" => $this->ends_at,
            "is_active" => $this->is_active,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "method" => [
                "uuid" => $this->method->uuid,

                "name" => $this->method->name,

                "code" => $this->method->code,
            ],

            "shipping_amount" => (float) $this->shipping_amount,

            "currency" => $this->currency,

            "delivery" => [
                "min_days" => $this->delivery["min_days"],

                "max_days" => $this->delivery["max_days"],
            ],

            "is_free" => $this->is_free,
        ];
    }
}

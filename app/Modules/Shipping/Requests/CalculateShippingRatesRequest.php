<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Requests;

use App\Core\Requests\BaseRequest;

final class CalculateShippingRatesRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "cart_uuid" => ["required", "uuid"],

            // Saved customer address (authenticated customers).
            "customer_address_uuid" => ["nullable", "uuid"],

            // Inline destination (required for guest checkout, optional otherwise).
            "guest_token" => ["nullable", "string", "max:120"],
            "country_code" => ["nullable", "string", "max:2"],
            "country" => ["nullable", "string", "max:2"],
            "state_code" => ["nullable", "string", "max:10"],
            "state" => ["nullable", "string", "max:10"],
            "postal_code" => ["nullable", "string", "max:20"],
        ];
    }
}

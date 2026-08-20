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

            "customer_address_uuid" => ["required", "uuid"],
        ];
    }
}

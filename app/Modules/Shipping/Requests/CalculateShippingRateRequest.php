<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Requests;

use App\Core\Requests\BaseRequest;

class CalculateShippingRateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "country_code" => ["required", "string", "size:2"],

            "state_code" => ["nullable", "string", "max:20"],

            "postal_code" => ["required", "string", "max:20"],

            "order_amount" => ["required", "numeric", "min:0"],

            "weight" => ["required", "numeric", "min:0"],
        ];
    }
}

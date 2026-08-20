<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Requests\Admin;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ShippingMethodRequest extends BaseRequest
{
    public function rules(): array
    {
        $uuid = $this->route("uuid");

        return [
            "name" => ["required", "string", "max:150"],

            "code" => [
                "required",
                "string",
                "max:50",
                Rule::unique("shipping_methods", "code")->ignore($uuid, "uuid"),
            ],

            "description" => ["nullable", "string"],

            "min_delivery_days" => ["required", "integer", "min:0"],

            "max_delivery_days" => [
                "required",
                "integer",
                "gte:min_delivery_days",
            ],

            "is_active" => ["sometimes", "boolean"],

            "sort_order" => ["sometimes", "integer", "min:0"],
        ];
    }
}

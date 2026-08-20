<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Requests\Admin;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ShippingZoneRequest extends BaseRequest
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
                Rule::unique("shipping_zones", "code")->ignore($uuid, "uuid"),
            ],

            "description" => ["nullable", "string"],

            "countries" => ["nullable", "array"],

            "countries.*" => ["string", "size:2"],

            "states" => ["nullable", "array"],

            "states.*" => ["string", "max:20"],

            "postal_codes" => ["nullable", "array"],

            "postal_codes.*" => ["string", "max:20"],

            "is_active" => ["sometimes", "boolean"],

            "sort_order" => ["sometimes", "integer", "min:0"],
        ];
    }
}

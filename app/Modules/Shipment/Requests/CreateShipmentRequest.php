<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Requests;
use App\Core\Requests\BaseRequest;
final class CreateShipmentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "order_uuid" => ["required", "uuid"],
            "carrier" => ["nullable", "string", "max:100"],
            "service" => ["nullable", "string", "max:100"],
            "tracking_number" => ["nullable", "string", "max:150"],
            "tracking_url" => ["nullable", "url", "max:500"],
            "notes" => ["nullable", "string", "max:2000"],
            "items" => ["required", "array", "min:1"],
            "items.*.order_item_uuid" => ["required", "uuid"],
            "items.*.quantity" => ["required", "numeric", "gt:0"],
        ];
    }
}

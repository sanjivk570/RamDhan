<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Requests;
use App\Core\Requests\BaseRequest;
final class UpdateShipmentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "status" => ["nullable", "string", "max:30"],
            "carrier" => ["nullable", "string", "max:100"],
            "service" => ["nullable", "string", "max:100"],
            "tracking_number" => ["nullable", "string", "max:150"],
            "tracking_url" => ["nullable", "url", "max:500"],
            "notes" => ["nullable", "string", "max:2000"],
        ];
    }
}

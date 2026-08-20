<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "shipment_number" => $this->shipment_number,
            "order_id" => $this->order_id,
            "status" => $this->status,
            "carrier" => $this->carrier,
            "service" => $this->service,
            "tracking_number" => $this->tracking_number,
            "tracking_url" => $this->tracking_url,
            "shipping_address" => $this->shipping_address,
            "shipped_at" => $this->shipped_at,
            "delivered_at" => $this->delivered_at,
            "items" => $this->whenLoaded("items"),
        ];
    }
}

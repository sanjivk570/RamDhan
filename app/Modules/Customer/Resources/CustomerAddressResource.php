<?php

declare(strict_types=1);

namespace App\Modules\Customer\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "type" => $this->type,

            "label" => $this->label,

            "first_name" => $this->first_name,

            "last_name" => $this->last_name,

            "full_name" => trim($this->first_name . " " . $this->last_name),

            "company" => $this->company,

            "address_line_1" => $this->address_line_1,

            "address_line_2" => $this->address_line_2,

            "landmark" => $this->landmark,

            "city" => $this->city,

            "state" => $this->state,

            "state_code" => $this->state_code,

            "postal_code" => $this->postal_code,

            "country" => $this->country,

            "country_code" => $this->country_code,

            "country_code_phone" => $this->country_code_phone,

            "phone" => $this->phone,

            "is_default" => $this->is_default,

            "is_active" => $this->is_active,

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

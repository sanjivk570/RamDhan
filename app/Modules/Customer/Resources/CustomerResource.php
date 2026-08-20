<?php

declare(strict_types=1);

namespace App\Modules\Customer\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "customer_code" => $this->customer_code,

            "first_name" => $this->first_name,

            "last_name" => $this->last_name,

            "full_name" => $this->full_name,

            "email" => $this->email,

            "country_code" => $this->country_code,

            "mobile" => $this->mobile,

            "avatar" => $this->avatar,

            "is_active" => $this->is_active,

            "email_verified_at" => $this->email_verified_at,

            "mobile_verified_at" => $this->mobile_verified_at,

            "last_login_at" => $this->last_login_at,

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

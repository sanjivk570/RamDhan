<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "supplier_code" => $this->supplier_code,

            "company_name" => $this->company_name,

            "display_name" => $this->display_name,

            "contact_person" => $this->contact_person,

            "email" => $this->email,

            "country_code" => $this->country_code,

            "mobile" => $this->mobile,

            "alternate_mobile" => $this->alternate_mobile,

            "website" => $this->website,

            "gstin" => $this->gstin,

            "pan" => $this->pan,

            "payment_terms_days" => $this->payment_terms_days,

            "credit_limit" => $this->credit_limit,

            "notes" => $this->notes,

            "is_active" => $this->is_active,

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

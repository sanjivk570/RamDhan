<?php

declare(strict_types=1);

namespace App\Modules\Tax\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            
            "uuid" => $this->uuid,

            "name" => $this->name,

            "rate" => $this->rate,

            "country_code" => $this->country_code,

            "state_code" => $this->state_code,

            "is_active" => $this->is_active,

            "priority" => $this->priority,

            "tax_class" => new TaxClassResource($this->whenLoaded("taxClass")),

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

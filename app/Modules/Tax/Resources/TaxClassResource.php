<?php

declare(strict_types=1);

namespace App\Modules\Tax\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,

            "uuid" => $this->uuid,

            "name" => $this->name,

            "code" => $this->code,

            "description" => $this->description,

            "is_active" => $this->is_active,

            "sort_order" => $this->sort_order,

            "rates_count" => $this->when(
                isset($this->rates_count),
                $this->rates_count
            ),

            "rates" => TaxRateResource::collection($this->whenLoaded("rates")),

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

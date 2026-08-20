<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "name" => $this->name,
            "slug" => $this->slug,
            "type" => $this->type,
            "sort_order" => $this->sort_order,
            "is_active" => $this->is_active,

            "values" => AttributeValueResource::collection(
                $this->whenLoaded("values")
            ),

            "values_count" => $this->when(
                isset($this->values_count),
                $this->values_count
            ),

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}

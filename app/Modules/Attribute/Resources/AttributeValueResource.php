<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "value" => $this->value,
            "slug" => $this->slug,
            "display_value" => $this->display_value,
            "sort_order" => $this->sort_order,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}

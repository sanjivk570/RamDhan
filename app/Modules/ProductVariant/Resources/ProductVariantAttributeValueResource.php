<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantAttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->attributeValue?->uuid,

            "value" => $this->attributeValue?->value,

            "attribute" => [
                "uuid" => $this->attributeValue?->attribute?->uuid,

                "name" => $this->attributeValue?->attribute?->name,
            ],
        ];
    }
}

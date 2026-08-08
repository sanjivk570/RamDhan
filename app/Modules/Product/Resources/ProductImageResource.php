<?php

declare(strict_types=1);

namespace App\Modules\Product\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Product image API resource.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ProductImageResource extends JsonResource
{
    /**
     * Transform the product image resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'alt_text' => $this->alt_text,
            'sort_order' => $this->sort_order,
            'is_primary' => $this->is_primary,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
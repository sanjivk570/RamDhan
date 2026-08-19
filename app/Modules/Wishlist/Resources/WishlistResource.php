<?php

declare(strict_types=1);

namespace App\Modules\Wishlist\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
final class WishlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "customer_id" => $this->customer_id,
            "product_id" => $this->product_id,
            "product_variant_id" => $this->product_variant_id,
            "created_at" => $this->created_at,
        ];
    }
}

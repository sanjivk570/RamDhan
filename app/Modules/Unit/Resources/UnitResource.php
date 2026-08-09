<?php

declare(strict_types=1);

namespace App\Modules\Unit\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "name" => $this->name,

            "code" => $this->code,

            "symbol" => $this->symbol,

            "decimal_places" => $this->decimal_places,

            "is_active" => $this->is_active,

            "sort_order" => $this->sort_order,

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

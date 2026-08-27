<?php

declare(strict_types=1);

namespace App\Modules\Slider\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform slider data into a JSON API resource.
 *
 * @package App\Modules\Slider\Resources
 * @author Sanjiv Kumar Kushwaha
 */
class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'      => $this->uuid,
            'name'      => $this->name,
            'code'      => $this->code,
            'placement' => $this->placement,
            'is_active' => $this->is_active,

            'items' => $this->whenLoaded(
                'items',
                fn () => SliderItemResource::collection($this->items)
            ),

            'active_items' => $this->whenLoaded(
                'activeItems',
                fn () => SliderItemResource::collection($this->activeItems)
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

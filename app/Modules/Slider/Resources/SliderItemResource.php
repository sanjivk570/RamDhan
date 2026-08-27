<?php

declare(strict_types=1);

namespace App\Modules\Slider\Resources;

use App\Modules\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform slider item data into a JSON API resource.
 *
 * @package App\Modules\Slider\Resources
 * @author Sanjiv Kumar Kushwaha
 */
class SliderItemResource extends JsonResource
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
            'uuid'        => $this->uuid,
            'slider_id'   => $this->slider_id,
            'title'       => $this->title,
            'subtitle'    => $this->subtitle,
            'button_text' => $this->button_text,
            'button_url'  => $this->button_url,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'starts_at'   => $this->starts_at,
            'ends_at'     => $this->ends_at,

            'images' => $this->whenLoaded(
                'media',
                fn () => MediaResource::collection($this->media)
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
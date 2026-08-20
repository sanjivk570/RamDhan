<?php

declare(strict_types=1);

namespace App\Modules\Category\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Media\Resources\MediaResource;

class CategoryResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {

        return [
            'id' => $this->id,

            'uuid' => $this->uuid,

            'name' => $this->name,

            'slug' => $this->slug,

            'description' => $this->description,

            //'image' => $this->image,

            'images' => MediaResource::collection(
                $this->whenLoaded('media')
            ),

            'is_active' => $this->is_active,

            'sort_order' => $this->sort_order,

            'parent' => $this->whenLoaded(
                'parent',
                fn () => [
                    'id' => $this->parent?->id,
                    'uuid' => $this->parent?->uuid,
                    'name' => $this->parent?->name,
                    'slug' => $this->parent?->slug,
                ]
            ),

            'children' => $this->whenLoaded(
                'children',
                fn () => $this->children->map(
                    fn ($child) => [
                        'uuid' => $child->uuid,
                        'name' => $child->name,
                        'slug' => $child->slug,
                    ]
                )->values()
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
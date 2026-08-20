<?php

declare(strict_types=1);

namespace App\Modules\Media\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $disk = Storage::disk($this->disk);
        $url = method_exists($disk, 'url') ? $disk->url($this->path) : null;

        return [
            "uuid" => $this->uuid,

            "original_name" => $this->original_name,

            "file_name" => $this->file_name,

            "url" => $url,

            "path" => $this->path,

            "disk" => $this->disk,

            "mime_type" => $this->mime_type,

            "size" => $this->size,

            "size_kb" => round($this->size / 1024, 2),

            "title" => $this->title,

            "alt_text" => $this->alt_text,

            "description" => $this->description,

            "type" => $this->type,

            "sort_order" => $this->sort_order,

            "is_primary" => $this->is_primary,

            "mediable_type" => $this->mediable_type,

            "mediable_id" => $this->mediable_id,

            "collection" => $this->collection,

            "created_at" => $this->created_at,

            "updated_at" => $this->updated_at,
        ];
    }
}

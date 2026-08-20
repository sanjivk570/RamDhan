<?php

declare(strict_types=1);

namespace App\Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Media extends Model
{
    use SoftDeletes;

    protected $table = "media";

    protected $fillable = [
        "uuid",
        "mediable_type",
        "mediable_id",
        "original_name",
        "file_name",
        "collection",
        "disk",
        "path",
        "mime_type",
        "size",
        "title",
        "alt_text",
        "description",
        "type",
        "sort_order",
        "is_primary",
    ];

    protected $casts = [
        "size" => "integer",
        "sort_order" => "integer",
        "is_primary" => "boolean",
    ];

    /**
     * Generate UUID automatically.
     */
    protected static function booted(): void
    {
        static::creating(function (Media $media) {
            if (empty($media->uuid)) {
                $media->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Polymorphic relationship.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    use SoftDeletes;

    protected $table = "attribute_values";

    protected $fillable = [
        "uuid",
        "attribute_id",
        "value",
        "slug",
        "display_value",
        "sort_order",
        "is_active",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "sort_order" => "integer",
    ];

    /**
     * Generate UUID automatically.
     */
    protected static function booted(): void
    {
        static::creating(function (AttributeValue $attributeValue) {
            if (empty($attributeValue->uuid)) {
                $attributeValue->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Parent attribute.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, "attribute_id");
    }
}

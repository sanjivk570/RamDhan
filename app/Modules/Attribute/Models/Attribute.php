<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use SoftDeletes;

    protected $table = "attributes";

    protected $fillable = [
        "uuid",
        "name",
        "slug",
        "type",
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
        static::creating(function (Attribute $attribute) {
            if (empty($attribute->uuid)) {
                $attribute->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Attribute values.
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, "attribute_id");
    }
}

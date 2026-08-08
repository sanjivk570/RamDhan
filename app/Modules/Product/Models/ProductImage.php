<?php

declare(strict_types=1);

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Product image model.
 *
 * Represents an image associated with a product.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ProductImage extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'product_id',
        'image_path',
        'image_url',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Bootstrap the product image model.
     *
     * Create UUID automatically when creating a new image.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (ProductImage $image) {
            if (empty($image->uuid)) {
                $image->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the product associated with the image.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
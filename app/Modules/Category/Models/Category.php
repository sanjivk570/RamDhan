<?php

namespace App\Modules\Category\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Modules\Product\Models\Product;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->uuid)) {
                $category->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'parent_id'
        );
    }

    /**
     * Child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(
            Category::class,
            'parent_id'
        );
    }

    /**
     * Category belongs to many products.
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_categories'
        );
    }
}
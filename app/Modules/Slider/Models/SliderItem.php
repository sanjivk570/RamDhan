<?php

namespace App\Modules\Slider\Models;

use App\Modules\Media\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * SliderItem model.
 *
 * Represents an individual slide within a slider. Its image(s) are
 * attached through the polymorphic media table using the
 * 'slider' collection.
 *
 * @package App\Modules\Slider\Models
 * @author Sanjiv Kumar Kushwaha
 */
class SliderItem extends Model
{
    use SoftDeletes;
    //use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'slider_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'slider_id',
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Bootstrap the slider item model.
     *
     * Automatically generates a UUID when a new slider item is created.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (SliderItem $sliderItem) {
            if (empty($sliderItem->uuid)) {
                $sliderItem->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * The slider this item belongs to.
     *
     * @return BelongsTo
     */
    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }

    /**
     * All media attached to this slide (images/videos).
     *
     * Uses the 'slider' media collection.
     *
     * @return MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('collection', 'slider');
    }

    /**
     * Image media attached to this slide.
     *
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('collection', 'slider')
            ->where('type', 'image')
            ->orderBy('sort_order');
    }

    /**
     * The primary (featured) image of the slide, if any.
     *
     * @return Media|null
     */
    public function getPrimaryImageAttribute()
    {
        return $this->media()
            ->where('is_primary', true)
            ->orderBy('sort_order')
            ->first();
    }
}
<?php

namespace App\Modules\Slider\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Slider model.
 *
 * Represents a named group of slider items (slides) that are
 * rendered together at a given placement on the storefront.
 *
 * @package App\Modules\Slider\Models
 * @author Sanjiv Kumar Kushwaha
 */
class Slider extends Model
{
    use SoftDeletes;
    use HasUuid;
    //use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sliders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'code',
        'placement',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Bootstrap the slider model.
     *
     * Automatically generates a UUID when a new slider is created.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Slider $slider) {
            if (empty($slider->uuid)) {
                $slider->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * All slider items belonging to this slider.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(SliderItem::class, 'slider_id')
            ->orderBy('sort_order');
    }

    /**
     * Only active slider items belonging to this slider.
     *
     * Restricts items to those that are active and currently
     * within their (optional) scheduling window.
     *
     * @return HasMany
     */
    public function activeItems(): HasMany
    {
        $now = now();

        return $this->items()
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }
}
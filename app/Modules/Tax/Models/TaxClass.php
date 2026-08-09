<?php

declare(strict_types=1);

namespace App\Modules\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxClass extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tax_classes";

    protected $fillable = [
        "uuid",
        "name",
        "code",
        "description",
        "is_active",
        "sort_order",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "sort_order" => "integer",
    ];

    /**
     * Tax rates belonging to this class.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(TaxRate::class, "tax_class_id");
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Tax\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tax_rates";

    protected $fillable = [
        "uuid",
        "tax_class_id",
        "name",
        "rate",
        "country_code",
        "state_code",
        "is_active",
        "priority",
    ];

    protected $casts = [
        "rate" => "decimal:2",
        "is_active" => "boolean",
        "priority" => "integer",
    ];

    /**
     * Tax class relationship.
     */
    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class, "tax_class_id");
    }
}

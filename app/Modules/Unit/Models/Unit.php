<?php

declare(strict_types=1);

namespace App\Modules\Unit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "units";

    protected $fillable = [
        "uuid",
        "name",
        "code",
        "symbol",
        "decimal_places",
        "is_active",
        "sort_order",
    ];

    protected $casts = [
        "decimal_places" => "integer",
        "is_active" => "boolean",
        "sort_order" => "integer",
    ];
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
final class OrderStatusHistory extends Model
{
    protected $fillable = [
        "uuid",
        "order_id",
        "from_status",
        "to_status",
        "changed_by",
        "source",
        "note",
    ];
    protected static function booted(): void
    {
        static::creating(
            fn(OrderStatusHistory $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
final class SalesReturn extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "uuid",
        "return_number",
        "order_id",
        "customer_id",
        "status",
        "refund_status",
        "total_amount",
        "reason",
        "customer_note",
        "admin_note",
        "created_by",
        "processed_by",
        "approved_at",
        "rejected_at",
    ];
    protected function casts(): array
    {
        return [
            "total_amount" => "decimal:2",
            "approved_at" => "datetime",
            "rejected_at" => "datetime",
        ];
    }
    protected static function booted(): void
    {
        static::creating(function (SalesReturn $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->return_number ??=
                "RET-" .
                now()->format("Ym") .
                "-" .
                str_pad(
                    (string) ((int) self::withTrashed()->max("id") + 1),
                    6,
                    "0",
                    "0"
                );
        });
    }
    public function items(): HasMany
    {
        return $this->hasMany(SalesReturnItem::class);
    }
}

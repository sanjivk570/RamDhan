<?php
namespace App\Modules\Purchase\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class PurchaseReturn extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "uuid",
        "return_number",
        "supplier_id",
        "goods_receipt_id",
        "created_by",
        "status",
        "return_date",
        "total_amount",
        "currency_code",
        "reason",
        "posted_at",
    ];
    protected $casts = [
        "return_date" => "date",
        "total_amount" => "decimal:2",
        "posted_at" => "datetime",
    ];
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->return_number ??=
                "PRET-" .
                now()->format("YmdHis") .
                "-" .
                Str::upper(Str::random(4));
        });
    }
    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}

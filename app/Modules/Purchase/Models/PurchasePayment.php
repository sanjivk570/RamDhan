<?php
namespace App\Modules\Purchase\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PurchasePayment extends Model
{
    protected $fillable = [
        "uuid",
        "payment_number",
        "supplier_id",
        "purchase_invoice_id",
        "created_by",
        "status",
        "payment_date",
        "amount",
        "currency_code",
        "payment_method",
        "reference_number",
        "bank_account",
        "notes",
    ];
    protected $casts = ["payment_date" => "date", "amount" => "decimal:2"];
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->uuid ??= (string) Str::uuid();
            $m->payment_number ??=
                "PPAY-" .
                now()->format("YmdHis") .
                "-" .
                Str::upper(Str::random(4));
        });
    }
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, "purchase_invoice_id");
    }
}

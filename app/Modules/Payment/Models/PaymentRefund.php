<?php

declare(strict_types=1);

namespace App\Modules\Payment\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
final class PaymentRefund extends Model
{
    protected $fillable = [
        "uuid",
        "order_id",
        "payment_transaction_id",
        "provider",
        "status",
        "provider_refund_id",
        "amount",
        "currency_code",
        "reason",
        "payload",
    ];
    protected function casts(): array
    {
        return ["amount" => "decimal:2", "payload" => "array"];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(PaymentRefund $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
}

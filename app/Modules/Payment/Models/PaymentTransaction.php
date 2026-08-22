<?php

declare(strict_types=1);

namespace App\Modules\Payment\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Modules\Order\Models\Order;
final class PaymentTransaction extends Model
{
    protected $fillable = [
        "uuid",
        "payment_intent_id",
        "order_id",
        "provider",
        "transaction_type",
        "status",
        "provider_transaction_id",
        "amount",
        "currency_code",
        "payment_method",
        "reference_number",
        "payload",
        "failure_reason",
    ];
    protected function casts(): array
    {
        return ["amount" => "decimal:2", "payload" => "array"];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(PaymentTransaction $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function paymentIntent(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Payment\Models\PaymentIntent::class, 'payment_intent_id');
    }
}

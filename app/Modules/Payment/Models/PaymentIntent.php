<?php

declare(strict_types=1);

namespace App\Modules\Payment\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class PaymentIntent extends Model
{
    protected $fillable = [
        "uuid",
        "order_id",
        "customer_id",
        "provider",
        "method",
        "status",
        "provider_reference",
        "amount",
        "currency_code",
        "provider_payload",
        "provider_response",
        "expires_at",
    ];
    protected function casts(): array
    {
        return [
            "amount" => "decimal:2",
            "provider_payload" => "array",
            "provider_response" => "array",
            "expires_at" => "datetime",
        ];
    }
    protected static function booted(): void
    {
        static::creating(
            fn(PaymentIntent $m) => ($m->uuid ??= (string) Str::uuid())
        );
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Order\Models\Order::class);
    }
}

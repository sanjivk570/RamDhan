<?php

declare(strict_types=1);

namespace App\Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerAddress extends Model
{
    use SoftDeletes;

    protected $table = "customer_addresses";

    protected $fillable = [
        "uuid",
        "customer_id",
        "type",
        "label",
        "first_name",
        "last_name",
        "company",
        "address_line_1",
        "address_line_2",
        "landmark",
        "city",
        "state",
        "state_code",
        "postal_code",
        "country",
        "country_code",
        "country_code_phone",
        "phone",
        "is_default",
        "is_active",
    ];

    protected function casts(): array
    {
        return [
            "is_default" => "boolean",
            "is_active" => "boolean",
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CustomerAddress $address) {
            if (empty($address->uuid)) {
                $address->uuid = (string) Str::uuid();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }
}

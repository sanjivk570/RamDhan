<?php

declare(strict_types=1);

namespace App\Modules\Customer\Models;

use App\Modules\Customer\Models\CustomerAddress;
use App\Modules\Auth\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = "customers";

    protected $fillable = [
        "uuid",
        "customer_code",
        "first_name",
        "last_name",
        "email",
        "country_code",
        "mobile",
        "avatar",
        "password",
        "email_verified_at",
        "mobile_verified_at",
        "last_login_at",
        "is_active",
    ];

    protected $hidden = ["id", "password", "remember_token"];

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "mobile_verified_at" => "datetime",
            "last_login_at" => "datetime",
            "password" => "hashed",
            "is_active" => "boolean",
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }

            if (empty($customer->customer_code)) {
                $customer->customer_code = "CUS-" . strtoupper(Str::random(8));
            }
        });
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class, "customer_id");
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . " " . $this->last_name);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

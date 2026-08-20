<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use App\Modules\Auth\Notifications\ResetPasswordNotification;
use App\Modules\Supplier\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User model.
 *
 * Represents an authenticated user within the application and
 * provides authentication, authorization, notifications,
 * API token management, and soft delete functionality.
 *
 * @package App\Modules\User\Models
 * @author Sanjiv Kumar Kushwaha
 */
class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes,
        HasRoles;
        //HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'supplier_id',
        'user_type',
        'is_primary_supplier_user',
        'first_name',
        'last_name',
        'email',
        'country_code',
        'mobile',
        'avatar',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden during serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected string $guard_name = 'web';

    /**
     * Get the model's attribute casting definitions.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'mobile_verified_at' => 'datetime',
            'last_login_at'      => 'datetime',
            'password'           => 'hashed',
            'is_active'          => 'boolean',
            'supplier_id' => 'integer',
            'is_primary_supplier_user' => 'boolean',
        ];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Bootstrap the model and register its event listeners.
     *
     * Automatically generates a UUID when a new user is created.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {

            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }

        });
    }

    /**
     * Send the password reset notification.
     *
     * Overrides Laravel's default password reset notification
     * with a custom notification implementation.
     *
     * @param string $token The password reset token.
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function isSupplier(): bool
    {
        return $this->user_type === 'supplier' && $this->supplier_id !== null;
    }

}
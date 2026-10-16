<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['restaurant_id', 'name', 'email', 'phone', 'password', 'pin_code', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'password_changed_at'])]
#[Hidden(['password', 'pin_code', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
            'pin_code' => 'hashed',
        ];
    }

    /**
     * Determine if Two-Factor Authentication is enabled and confirmed.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Determine if Master PIN is configured.
     */
    public function hasPinCode(): bool
    {
        return ! empty($this->pin_code);
    }

    /**
     * Get the restaurant that the user belongs to.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'password',
        'google_id',
        'avatar',
        'role',
        'status',
        'municipality_id',
        'id_document_path',
        'id_document_type',
        'id_verification_status',
        'reset_code',
        'reset_code_expires_at',
        'two_factor_secret',
        'two_factor_email_code',
        'two_factor_code_expires_at',
        'two_factor_enabled',
        'office_id',
        'requires_first_login_otp',
        'first_login_otp_code',
        'first_login_otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'reset_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'          => 'datetime',
            'password'                   => 'hashed',
            'reset_code_expires_at'      => 'datetime',
            'two_factor_code_expires_at' => 'datetime',
            'two_factor_enabled'         => 'boolean',
            'requires_first_login_otp'   => 'boolean',
            'first_login_otp_expires_at' => 'datetime',
            // Sensitive PII encrypted at rest (AES-256 via the app key).
            // See the encrypt_sensitive_pii_columns migration for the
            // one-time backfill of pre-existing plaintext values.
            'phone'                      => 'encrypted',
            'address'                    => 'encrypted',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function requiresTwoFactor(): bool
    {
        return in_array($this->role, ['admin', 'office']);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function citizenRequests()
    {
        return $this->hasMany(CitizenRequest::class);
    }
}

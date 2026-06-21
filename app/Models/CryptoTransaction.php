<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoTransaction extends Model
{
    protected $fillable = [
        'citizen_request_id', 'currency', 'amount_usd', 'amount_crypto',
        'crypto_price_usd', 'wallet_address', 'tx_hash', 'status', 'expires_at',
    ];

    protected $casts = [
        'expires_at'    => 'datetime',
        'amount_crypto' => 'decimal:8',
    ];

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() && $this->status === 'pending';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CitizenRequest extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'office_id',
        'full_name', 'phone', 'email', 'address',
        'notes', 'uploaded_document', 'submitted_at',
        'status', 'certificate_path', 'approved_at',
        'payment_method', 'payment_status',
        'uuid', 'response_document', 'response_note',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    protected $casts = [
        'approved_at' => 'datetime',
        // Sensitive PII encrypted at rest (AES-256 via the app key).
        // full_name/email here are the citizen's *submitted* details for
        // this specific request (not their account name/email) — this is
        // the data certificates and payment receipts are generated from,
        // decrypted transparently the moment it's accessed.
        'full_name'   => 'encrypted',
        'phone'       => 'encrypted',
        'email'       => 'encrypted',
        'address'     => 'encrypted',
        'notes'       => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function cryptoTransactions()
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    public function localPayments()
    {
        return $this->hasMany(LocalPayment::class);
    }

    public function serviceApplication()
    {
        return $this->hasOne(ServiceApplication::class);
    }

    public function rating()
    {
        return $this->hasOne(CitizenRequestRating::class);
    }

    public function histories()
    {
        return $this->hasMany(CitizenRequestHistory::class)->orderBy('created_at', 'asc');
    }

    public function messages()
    {
        return $this->hasMany(CitizenRequestMessage::class)->orderBy('created_at', 'asc');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function logHistory(string $toStatus, ?int $userId = null, ?string $note = null): void
    {
        $this->histories()->create([
            'user_id'     => $userId,
            'from_status' => $this->getOriginal('status') ?? $this->status,
            'to_status'   => $toStatus,
            'note'        => $note,
        ]);
    }
}

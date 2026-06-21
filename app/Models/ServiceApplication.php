<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'user_id',
        'service_id',
        'office_id',
        'full_name',
        'phone',
        'email',
        'address',
        'notes',
        'citizen_lat',
        'citizen_lng',
        'status',
        'rejection_reason',
        'submitted_at',
        'citizen_request_id',
        'certificate_path',
        'approved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'citizen_lat'  => 'decimal:7',
        'citizen_lng'  => 'decimal:7',
        'approved_at'  => 'datetime',
        // Sensitive PII encrypted at rest (AES-256 via the app key).
        'full_name'    => 'encrypted',
        'phone'        => 'encrypted',
        'email'        => 'encrypted',
        'address'      => 'encrypted',
        'notes'        => 'encrypted',
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

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class, 'application_id');
    }

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'   => '#f59e0b',
            'reviewing' => '#a5b4fc',
            'approved'  => '#6ee7b7',
            'rejected'  => '#f87171',
            'completed' => '#34d399',
            default     => '#94a3b8',
        };
    }

    public static function generateReference(): string
    {
        $year  = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
      
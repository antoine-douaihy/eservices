<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'office_id', 'user_id', 'citizen_request_id',
        'title', 'scheduled_at', 'duration_minutes',
        'status', 'notes', 'reminder_sent',
        'requested_by_citizen', 'citizen_notes',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }
}

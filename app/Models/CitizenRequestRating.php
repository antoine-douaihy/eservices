<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitizenRequestRating extends Model
{
    protected $fillable = ['citizen_request_id', 'user_id', 'stars', 'comment', 'office_response', 'responded_at'];

    protected $casts = ['responded_at' => 'datetime'];

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

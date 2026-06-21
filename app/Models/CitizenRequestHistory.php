<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitizenRequestHistory extends Model
{
    protected $fillable = ['citizen_request_id', 'user_id', 'from_status', 'to_status', 'note'];

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

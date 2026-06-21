<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitizenRequestMessage extends Model
{
    protected $fillable = ['citizen_request_id', 'user_id', 'content', 'is_office'];

    protected $casts = ['is_office' => 'boolean'];

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

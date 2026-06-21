<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalPayment extends Model
{
    protected $fillable = [
        'citizen_request_id', 'method', 'amount_usd',
        'account_details', 'reference_number', 'status',
    ];

    public function citizenRequest()
    {
        return $this->belongsTo(CitizenRequest::class);
    }
}

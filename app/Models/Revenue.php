<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenue extends Model
{
    protected $fillable = ['office_id', 'amount', 'description', 'transaction_date'];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}

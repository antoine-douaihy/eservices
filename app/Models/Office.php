<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'city',
        'phone',
        'email',
        'municipality_id',
        'is_active',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'working_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function totalRevenue(): float
    {
        return (float) $this->revenues()->sum('amount');
    }
}
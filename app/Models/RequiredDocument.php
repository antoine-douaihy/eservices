<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'name_ar',
        'notes',
        'is_mandatory',
        'sort_order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** Locale-aware display name, falls back to English. */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }
        return $this->name;
    }
}

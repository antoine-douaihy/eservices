<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'required_documents',
        'price',
        'currency',
        'processing_days',
        'office_id',
        'category_id',
        'group_uuid',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Auto-generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name) . '-' . Str::random(5);
            }
        });
    }

    // Relationships
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Every other per-office copy of this same logical service (i.e. the
     * same service offered at every municipality). Linked via group_uuid,
     * which is shared by every copy created together.
     */
    public function siblings()
    {
        if (empty($this->group_uuid)) {
            return static::query()->whereRaw('0 = 1');
        }
        return static::where('group_uuid', $this->group_uuid)->where('id', '!=', $this->id);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class)->orderBy('sort_order');
    }

    // Formatted price helper
    public function formattedPrice(): string
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Locale-aware display name. Falls back to the canonical English
     * `name` if no Arabic translation exists. Use this (not `name`)
     * anywhere a service title is shown to the user — `name` itself
     * stays English-only since it's used in cross-office matching
     * queries (e.g. finding the nearest office offering "this" service).
     */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }
        return $this->name;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->description_ar)) {
            return $this->description_ar;
        }
        return $this->description;
    }
}

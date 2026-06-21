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
        return static::where('group_uuid', $this-
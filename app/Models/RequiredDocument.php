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
        return $this->belongsTo(Servic
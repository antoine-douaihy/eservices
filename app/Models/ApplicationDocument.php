<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'required_document_id',
        'document_name',
        'file_path',
        'file_original_name',
        'mime_type',
        'file_size',
    ];

    public function application()
    {
        return $this->belongsTo(ServiceApplication::class, 'application_id');
    }

    public function requiredDocument()
    {
        return $this->belongsTo(RequiredDocument::class);
    }

    // Human-readable file size
    public function formattedSize(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}

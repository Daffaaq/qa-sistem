<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentHistorie extends Model
{
    use HasFactory;

    protected $table = 'document_histories';

    protected $fillable = [
        'document_id',
        'title_document',
        'file_document',
        'date_document',
        'time_document',
        'revision_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function getTimeDocumentAttribute($value)
    {
        return substr($value, 0, 8); // Format: HH:mm:ss
    }
}

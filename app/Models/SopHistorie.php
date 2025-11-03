<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopHistorie extends Model
{
    use HasFactory;

    protected $table = 'sop_histories';

    protected $fillable = [
        'sop_id',
        'title_document',
        'file_document',
        'date_document',
        'time_document',
        'revision_number',
        'is_active',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WiHistorie extends Model
{
    use HasFactory;

    protected $table = 'wi_histories';

    protected $fillable = [
        'wi_id',
        'title_document',
        'file_document',
        'date_document',
        'time_document',
        'revision_number',
        'is_active',
    ];

    public function wi()
    {
        return $this->belongsTo(Wi::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormHistorie extends Model
{
    use HasFactory;

    protected $table = 'form_histories';

    protected $fillable = [
        'form_id',
        'title_document',
        'file_document',
        'date_document',
        'time_document',
        'revision_number',
        'is_active',
        'keterangan',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}

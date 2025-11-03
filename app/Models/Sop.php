<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    use HasFactory;

    protected $table = 'sops';

    protected $fillable = [
        'document_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function histories()
    {
        return $this->hasMany(SopHistorie::class);
    }

    public function wis()
    {
        return $this->hasMany(Wi::class);
    }
}

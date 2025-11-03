<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';

    protected $fillable = [
        'wi_id',
    ];

    public function wi()
    {
        return $this->belongsTo(Wi::class);
    }

    public function histories()
    {
        return $this->hasMany(FormHistorie::class);
    }
}

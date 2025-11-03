<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wi extends Model
{
    use HasFactory;

    protected $table = 'wis';

    protected $fillable = [
        'sop_id',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    public function histories()
    {
        return $this->hasMany(WiHistorie::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}

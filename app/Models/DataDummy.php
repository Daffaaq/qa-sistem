<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDummy extends Model
{
    use HasFactory;

    protected $table = 'data_dummies';

    protected $fillable = [
        'bulan',
        'total_kirim',
        'tahun'
    ];
}

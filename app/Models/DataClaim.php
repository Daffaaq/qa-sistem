<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataClaim extends Model
{
    use HasFactory;

    protected $table = 'data_claims';

    // Field yang bisa diisi massal
    protected $fillable = [
        'tanggal_claim',
        'customer',
        'part_no',
        'problem',
        'quantity',
        'klasifikasi',
        'kategori',
        'file_evident',
    ];

    // Jika mau menggunakan tipe tanggal otomatis untuk tanggal_claim
    protected $dates = [
        'tanggal_claim',
        'created_at',
        'updated_at',
    ];

    public function getTimeDocumentAttribute($value)
    {
        return substr($value, 0, 8); // Format: HH:mm:ss
    }
}

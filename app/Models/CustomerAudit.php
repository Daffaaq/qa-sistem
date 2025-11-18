<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAudit extends Model
{
    use HasFactory;

    protected $table = 'customer_audits';

    // Field yang bisa diisi massal
    protected $fillable = [
        'nama_event',
        'deskripsi_event',
        'tanggal_mulai_event',
        'tanggal_selesai_event',
        'file_evident',
        'logo_customer',
    ];

    // Mengatur tipe tanggal otomatis
    protected $dates = [
        'tanggal_mulai_event',
        'tanggal_selesai_event',
        'created_at',
        'updated_at',
    ];

    public function getTimeDocumentAttribute($value)
    {
        return substr($value, 0, 8); // Format: HH:mm:ss
    }

    public function dataAudit()
    {
        return $this->hasMany(DataAudit::class, 'customer_audits_id', 'id');
    }
}

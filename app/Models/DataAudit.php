<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAudit extends Model
{
    use HasFactory;

    protected $table = 'data_audits';

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'temuan',
        'due_date',
        'status',
        'pic',
        'file_evident',
        'customer_audits_id',
        'keterangan',
    ];

    // Jika ingin mengubah format tanggal otomatis (opsional)
    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
    ];

    public function CustomerAudit()
    {
        return $this->belongsTo(CustomerAudit::class);
    }
}

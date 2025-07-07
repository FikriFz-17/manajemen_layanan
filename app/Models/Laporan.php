<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resi',
        'judul_masalah',
        'kategori',
        'status',
        'tanggal_pengajuan',
        'tanggal_selesai',
        'estimasi',
        'deskripsi',
        'penyelesaian',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

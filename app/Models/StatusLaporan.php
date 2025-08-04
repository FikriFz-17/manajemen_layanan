<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_id',
        'pengajuan_tanggal',
        'progress_tanggal',
        'selesai_tanggal',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }
}


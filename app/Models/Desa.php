<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kecamatan_id',
    ];

    public function kecamatan(){
        return $this->belongsTo(Kecamatan::class);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Kecamatan;

class InstansiApiController extends Controller
{
    public function getKecamatan(){
        $data = DB::table('kecamatans')
            ->select(
                'kecamatans.id as kecamatan_id',
                'kecamatans.nama as kecamatan_nama',
            )
            ->get();
        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function getDesa(Request $request){
        $kecamatanId = $request->query('kecamatan_id');

        if (!$kecamatanId) {
            return response()->json([
                'message' => 'kecamatan_id wajib diisi'
            ], 400);
        }

        $data = DB::table('desas')
            ->where('kecamatan_id', $kecamatanId)
            ->select(
                'desas.kode as kode_desa',
                'desas.nama as nama_desa'
            )
            ->get();

        return response()->json([
            'message' => 'Data desa berhasil diambil',
            'data' => $data
        ]);
    }

    public function getPemda(){
        $data = DB::table('pemdas')
            ->select('pemdas.id as pemda_id', 'pemdas.nama as pemda_nama')
            ->get();

        return response()->json([
            'message' => 'Data pemda berhasil diambil',
            'data' => $data
        ]);
    }
}

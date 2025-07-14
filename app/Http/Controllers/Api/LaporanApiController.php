<?php

namespace App\Http\Controllers\Api;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LaporanApiController extends Controller
{
    public function createLaporan(Request $request)
    {
        $user = Auth::user();

        if (empty($user->nama) || empty($user->instansi)) {
            return response()->json([
                'message' => 'Data pengguna tidak lengkap.',
                'errors' => [
                    'nama' => empty($user->nama) ? 'Nama belum diisi.' : $user->nama,
                    'instansi' => empty($user->instansi) ? 'Instansi belum diisi.' : $user->instansi,
                ]
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'masalah' => 'required|string',
            'deskripsi' => 'required|string',
        ], [
            'tanggal.required' => 'Tanggal tidak boleh kosong',
            'masalah.required' => 'Masalah tidak boleh kosong',
            'deskripsi.required' => 'Deskripsi tidak boleh kosong',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Generate resi format: dmy-no
        $tanggal = \Carbon\Carbon::parse($request->tanggal);
        $tanggalFormat = $tanggal->format('dmy'); // Contoh: 010725

        $no = 1;
        $resi = '';
        $exists = true;

        while ($exists) {
            $no_str = str_pad($no, 2, '0', STR_PAD_LEFT);
            $resi = $tanggalFormat . '-' . $no_str;
            $exists = Laporan::where('resi', $resi)->exists();
            $no++;
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $originalName = $file->getClientOriginalName();
            $lampiranPath = $file->storeAs('lampiran', $originalName, 'public');
        }

        // Simpan laporan ke DB
        $laporan_id = DB::table('laporans')->insertGetId([
            'user_id' => $user->id,
            'resi' => $resi,
            'judul_masalah' => $validated['masalah'],
            'status' => 'Pengajuan',
            'tanggal_pengajuan' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'lampiran' => $lampiranPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ambil kembali data lengkap laporan
        $laporan = DB::table('laporans')->where('id', $laporan_id)->first();

        return response()->json([
            'message' => 'Laporan berhasil dibuat.',
            'laporan' => $laporan
        ], 201);
    }

    public function getUserData(Request $request){
        $user = Auth::user();
        $laporan = DB::table('laporans')
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(status, 'Selesai', 'Progress', 'Pengajuan'),tanggal_pengajuan DESC")
            ->get()
            ->map(function($item){
                $item->lampiran_url = $item->lampiran ? asset('storage/' . rawurlencode($item->lampiran)) : null;
                $item->lampiran_name = $item->lampiran ? basename($item->lampiran) : null;
                return $item;
            });

        return response()->json([
            'message' => 'Succes',
            'laporan' => $laporan,
        ]);
    }

    public function getAllData(Request $request){
        $laporan = DB::table('laporans')
            ->join('users', 'laporans.user_id', '=', 'users.id')
            ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi'
            )
            ->orderByRaw("FIELD(laporans.status, 'Pengajuan', 'Progress', 'Selesai'),laporans.tanggal_pengajuan ASC")
            ->get()
            ->map(function($item){
                $item->lampiran_url = $item->lampiran ? asset('storage/' . rawurlencode($item->lampiran)) : null;
                $item->lampiran_name = $item->lampiran ? basename($item->lampiran) : null;
                return $item;
            });

        return response()->json([
            'message' => 'Success',
            'laporan' => $laporan
        ]);
    }

    public function getPublicData(){
        $data = DB::table('laporans')
            ->select(
                'laporans.resi as resi',
                'laporans.judul_masalah as masalah',
                'laporans.tanggal_pengajuan as tanggal_pengajuan',
                'laporans.status as status'
            )
            ->orderByRaw("laporans.tanggal_pengajuan DESC")->get();

        return response()->json([
            'message' => 'Success',
            'laporan' => $data
        ]);
    }
}

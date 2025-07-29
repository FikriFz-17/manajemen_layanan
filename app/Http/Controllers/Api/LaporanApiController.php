<?php

namespace App\Http\Controllers\Api;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Mail\sendSelesaiEmail;

class LaporanApiController extends Controller
{
    /**
     * Ajukan Laporan
     *
     * Endpoint untuk mengajukan laporan pengguna (User Akses)
     *
     * @authenticated
    */
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

    /**
     * Laporan Pengguna
     *
     * Endpoint untuk menampilkan data laporan pengguna (Users Akses)
     *
     * @authenticated
    */
    public function getUserData(Request $request){
        $user = Auth::user();
        $laporan = DB::table('laporans')
            ->select('id', 'resi', 'judul_masalah', 'status', 'kategori', 'tanggal_pengajuan', 'tanggal_selesai', 'estimasi', 'deskripsi', 'lampiran')
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

    /**
     * Laporan Semua Pengguna
     *
     * Endpoint untuk menampilkan semua data pengguna (Admin Akses)
     *
     * @authenticated
    */
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

    /**
     * Laporan Publik
     *
     * Endpoint untuk menampilkan data laporan publik
     *
     * @unauthenticated
    */
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

    /**
     * Tangani Laporan
     *
     * Endpoint untuk menangani laporan pengguna (Admin Akses)
     *
     * @authenticated
    */
    public function tanganiLaporan(Request $request, $id){
        $validated = $request->validate([
            'status' => 'required|string',
            'kategori' => 'required|string',
            'tanggal_selesai' => 'required|date',
            'deskripsi_penanganan' => 'required|string',
        ], [
            'status.required' => 'Silakan ubah status laporan terlebih dahulu.',
            'kategori.required' => 'Kategori laporan harus ditentukan.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'deskripsi_penanganan.required' => 'Deskripsi penanganan tidak boleh kosong.',
        ]);

        // Ambil tanggal pengajuan
        $laporan = DB::table('laporans')->where('id', $id)->first();
        $tanggal_pengajuan = $laporan->tanggal_pengajuan;

        if ($laporan->status === $validated['status']) {
            return response()->json(['message' => 'Status laporan belum diubah.'], 422);
        }

        // Hitung estimasi
        $estimasi = abs(\Carbon\Carbon::parse($validated['tanggal_selesai'])->diffInDays(\Carbon\Carbon::parse($tanggal_pengajuan)));
        DB::table('laporans')->where('id', $id)->update([
            'status' => $validated['status'],
            'kategori' => $validated['kategori'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'penyelesaian' => $validated['deskripsi_penanganan'],
            'estimasi' => $estimasi,
        ]);

        // Ambil data user
        $user = DB::table('users')->where('id', $laporan->user_id)->first();

        // Kirim Email ke user
        $emailData = [
            'nama' => $user->nama,
            'email' => $user->email,
            'resi' => $laporan->resi,
            'masalah' => $laporan->judul_masalah,
            'tanggal_pengajuan' => $laporan->tanggal_pengajuan,
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'estimasi' => $estimasi,
            'penyelesaian' => $validated['deskripsi_penanganan'],
            'status' => $validated['status'],
        ];

        if (strtolower($validated['status']) === 'selesai') {
            Mail::to($user->email)->send(new SendSelesaiEmail($emailData));
        } else {
            Mail::to($user->email)->send(new SendEmail($emailData));
        }

        return response()->json([
            'message' => 'Laporan berhasil diproses dan email notifikasi telah dikirim.',
            'laporan_id' => $id,
            'status' => $validated['status'],
            'estimasi' => $estimasi
        ]);
    }
}

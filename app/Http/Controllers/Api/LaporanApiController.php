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

        // Cek kelengkapan data user
        if (empty($user->nama) || empty($user->instansi)) {
            return response()->json([
                'message' => 'Data pengguna tidak lengkap.',
                'errors' => [
                    'nama' => empty($user->nama) ? 'Nama belum diisi.' : $user->nama,
                    'instansi' => empty($user->instansi) ? 'Instansi belum diisi.' : $user->instansi,
                ]
            ], 400);
        }

        // Validasi request
        $validated = $request->validate([
            'tanggal' => 'required|date|before_or_equal:today|after_or_equal:1900-01-01',
            'masalah' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'lampiran' => 'file|mimes:jpg,png,jpeg|max:5120'
        ], [
            'tanggal.required' => 'Tanggal tidak boleh kosong',
            'tanggal.before_or_equal' => 'Emangnya bisa liat masa depan? awokwokw',
            'tanggal.after_or_equal' => 'Orang purba jir',
            'masalah.required' => 'Masalah tidak boleh kosong',
            'masalah.max' => 'Masukkan judul masalah secara umum',
            'deskripsi.required' => 'Deskripsi tidak boleh kosong',
            'lampiran.mimes' => 'Lampiran harus berupa JPG, PNG, atau JPEG',
            'lampiran.max' => 'Lampiran maksimal 5MB',
        ]);

        // Generate resi format: dmy-no
        $tanggal = \Carbon\Carbon::parse($validated['tanggal']);
        $tanggalFormat = $tanggal->format('dmy');

        $no = 1;
        $resi = '';
        $exists = true;

        while ($exists) {
            $no_str = str_pad($no, 2, '0', STR_PAD_LEFT);
            $resi = $tanggalFormat . '-' . $no_str;
            $exists = \App\Models\Laporan::where('resi', $resi)->exists();
            $no++;
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $originalName = $file->getClientOriginalName();
            $lampiranPath = $file->storeAs('lampiran', $originalName, 'public');
        }

        DB::beginTransaction();

        try {
            // Insert laporan
            $laporanId = DB::table('laporans')->insertGetId([
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

            // Insert status laporan
            DB::table('status_laporans')->insert([
                'laporan_id' => $laporanId,
                'pengajuan_tanggal' => $validated['tanggal'],
                'progress_tanggal' => null,
                'selesai_tanggal' => null,
            ]);

            DB::commit();

            // Ambil data lengkap laporan
            $laporan = DB::table('laporans')->where('id', $laporanId)->first();

            return response()->json([
                'message' => 'Laporan berhasil diajukan.',
                'laporan' => $laporan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan laporan.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $chartData = DB::table('laporans')
            ->select(
                'laporans.tanggal_pengajuan as tanggal_pengajuan',
                'laporans.status as status'
            )
            ->get();

        $latestData = DB::table('laporans')
            ->select(
                'laporans.resi as resi',
                'laporans.judul_masalah as masalah',
                'laporans.tanggal_pengajuan as tanggal_pengajuan',
                'laporans.status as status',
                'laporans.lampiran as lampiran'
            )
            ->where('laporans.status', '!=', 'pengajuan')
            ->orderByRaw("laporans.tanggal_pengajuan DESC")
            ->limit(6)
            ->get()
            ->map(function($item){
                $item->lampiran_url = $item->lampiran ? asset('storage/' . $item->lampiran) : null;
                return $item;
            });

        return response()->json([
            'chart' => $chartData,
            'latest_laporan' => $latestData,
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

    /**
     * Semua Laporan Public
     *
     * Endpoint untuk mendapatkan semua aduan (Terpaginate)
     *
     * @unauthenticated
    */
    public function getAllPublicData(){
        $allData = DB::table('laporans')
            ->select(
                'laporans.resi as resi',
                'laporans.judul_masalah as masalah',
                'laporans.tanggal_pengajuan as tanggal_pengajuan',
                'laporans.status as status',
                'laporans.lampiran as lampiran'
            )
            ->where('laporans.status', '!=', 'pengajuan')
            ->orderByRaw("laporans.tanggal_pengajuan DESC")
            ->paginate(5);
        $allData->getCollection()->transform(function($item){
            $item->lampiran_url = $item->lampiran ? asset('storage/' . $item->lampiran) : null;
            return $item;
        });

        return response()->json([
                'all_data' => [
                    'data' => $allData->items(),
                    'total' => $allData->total(),
                    'per_page' => $allData->perPage(),
                ]
            ]);
    }

    private function searchAndFilter(Request $request, $role)
    {
        $user = Auth::user();
        $query = DB::table('laporans')
        ->join('users', 'laporans.user_id', '=', 'users.id')
        ->select(
            'laporans.id',
            'laporans.resi',
            'laporans.judul_masalah as masalah',
            'laporans.deskripsi as deskripsi',
            'laporans.tanggal_pengajuan as tanggal_pengajuan',
            'laporans.status as status',
            'laporans.lampiran as lampiran',
            'laporans.user_id as laporan_id',
            'laporans.kategori as kategori',
            'laporans.tanggal_selesai as tanggal_selesai',
            'laporans.penyelesaian as penyelesaian',
            'laporans.estimasi as estimasi',
            'users.nama as user_nama',
            'users.email as user_email',
            'users.instansi as user_instansi',
            'users.jenis_instansi as user_jenis_instansi'
        );

        if ($role === 'user') {
            $query->where('laporans.user_id', $user->id);
        } else if ($role === 'admin'){
            $query->orderByRaw("FIELD(laporans.status, 'Pengajuan', 'Progress', 'Selesai'),laporans.tanggal_pengajuan ASC");
        }

        // Search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(laporans.judul_masalah) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(laporans.resi) LIKE ?', ["%{$search}%"]);
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->where('laporans.status', $request->status);
        }

        // Kategori
        if ($request->filled('kategori')) {
            $query->where('laporans.kategori', $request->kategori);
        }

        // Jenis Instansi
        if ($request->filled('jenis_instansi')) {
            $query->where('users.jenis_instansi', $request->jenis_instansi);
        }

        // Tanggal
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
            $end   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

            $query->when($start, function ($q) use ($start) {
                $q->whereDate('laporans.tanggal_pengajuan', '>=', $start);
            });

            $query->when($end, function ($q) use ($end) {
                $q->whereDate('laporans.tanggal_pengajuan', '<=', $end);
            });
        }

        // Show entries
        $perPage = $request->input('per_page', 5);
        $data = $query->paginate($perPage);

        $data = $query->orderByDesc('laporans.tanggal_pengajuan')
            ->paginate($request->input('per_page', 5));

        $data->getCollection()->transform(function ($item) use ($role) {
            $item->lampiran_url = $item->lampiran ? asset('storage/' . $item->lampiran) : null;

            if ($role === 'public') {
                foreach (['user_id', 'user_nama', 'user_email', 'user_instansi', 'user_jenis_instansi', 'deskripsi', 'tanggal_selesai', 'kategori', 'penyelesaian', 'estimasi'] as $field) {
                    unset($item->$field);
                }
            }

            return $item;
        });

        $stats = DB::table('laporans')
        ->select(
            DB::raw("SUM(CASE WHEN status = 'Pengajuan' THEN 1 ELSE 0 END) as pengajuan"),
            DB::raw("SUM(CASE WHEN status = 'Progress' THEN 1 ELSE 0 END) as progress"),
            DB::raw("SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai")
        )
        ->first();

        $response = [
            'all_data' => [
                'data' => $data->items(),
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
            ]
        ];

        if ($role === 'admin') {
            $response['statistics'] = $stats;
        }

        return response()->json($response);
    }

    /**
     * Public Search & Filter
     *
     * Endpoint untuk search dan filter
     *
     * @unauthenticated
    */
    public function publicSearchFilter(Request $request) {
        return $this->searchAndFilter($request, 'public');
    }

    /**
     * Admin Search & Filter
     *
     * Endpoint untuk search dan filter
     *
     * @authenticated
    */
    public function adminSearchFilter(Request $request) {
        return $this->searchAndFilter($request, 'admin');
    }

    /**
     * User Search & Filter
     *
     * Endpoint untuk search dan filter
     *
     * @authenticated
    */
    public function userSearchFilter(Request $request) {
        return $this->searchAndFilter($request, 'user');
    }
}

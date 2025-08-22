<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Mail\sendSelesaiEmail;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    public function createlaporan(Request $request){
        $user = Auth::user();
        $validated = $request->validate([
            'tanggal' => 'required|date|before_or_equal:today|after_or_equal:1900-01-01',
            'masalah' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120'
        ], [
            'tanggal.required' => 'Tanggal tidak boleh kosong',
            'tanggal.before_or_equal' => 'Emangnya bisa liat masa depan? awokwokw',
            'tanggal.after_or_equal' => 'Orang purba jir',
            'masalah.required' => 'Masalah tidak boleh kosong',
            'masalah.max' => 'Masukkan judul masalah secara umum',
            'deskripsi.required' => 'Deskripsi tidak boleh kosong',
            'lampiran.mimes' => 'Lampiran harus berupa PDF, JPG, PNG, DOC, atau DOCX',
            'lampiran.max' => 'Lampiran maksimal 5MB',
        ]);

        // Generate resi format: dmy-no
        $tanggal = \Carbon\Carbon::parse($request->tanggal);
        $tanggalFormat = $tanggal->format('dmy'); // Contoh: 010825

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
            $originalName = $file->getClientOriginalName(); // nama asli file
            $lampiranPath = $file->storeAs('lampiran', $originalName, 'public');
        }

        // Mulai transaksi DB
        DB::beginTransaction();

        try {
            // Insert laporan dan ambil ID-nya
            $laporanId = DB::table('laporans')->insertGetId([
                'user_id' => $user->id,
                'resi' => $resi,
                'judul_masalah' => $validated['masalah'],
                'status' => 'Pengajuan',
                'tanggal_pengajuan' => $validated['tanggal'],
                'deskripsi' => $validated['deskripsi'],
                'lampiran' => $lampiranPath
            ]);

            // Insert ke tabel status_laporans
            DB::table('status_laporans')->insert([
                'laporan_id' => $laporanId,
                'pengajuan_tanggal' => $validated['tanggal'],
                'progress_tanggal' => null,
                'selesai_tanggal' => null,
            ]);

            DB::commit();
            return back()->with('success', 'Laporan berhasil diajukan. Pantau status laporan Anda di dashboard.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan laporan: ' . $e->getMessage());
        }
    }

    public function getAllData(Request $request){
        $per_page = $request->input('per_page', 5);
        $data = DB::table('laporans')
            ->join('users', 'laporans.user_id', '=', 'users.id')
            ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi',
                'users.jenis_instansi as user_jenis_instansi'
            )
            ->orderByRaw("FIELD(laporans.status, 'Pengajuan', 'Progress', 'Selesai'),laporans.tanggal_pengajuan ASC")
            ->paginate($per_page);

        $stats = DB::table('laporans')
        ->select(
            DB::raw("SUM(CASE WHEN status = 'Pengajuan' THEN 1 ELSE 0 END) as pengajuan"),
            DB::raw("SUM(CASE WHEN status = 'Progress' THEN 1 ELSE 0 END) as progress"),
            DB::raw("SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai")
        )
        ->first();

        return response()->json([
            'data' => [
                'all_data' => $data->items(),
                'total' => $data->total()
            ],
            'statistics' => $stats
        ]);
    }

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

    public function getUserData(Request $request){
        $per_page = $request->input('per_page', 5);
        $user = Auth::user();
        $data = DB::table('laporans')
            ->select('id', 'resi', 'judul_masalah', 'status', 'kategori', 'tanggal_pengajuan', 'tanggal_selesai', 'estimasi', 'deskripsi', 'lampiran')
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(status, 'Selesai', 'Progress', 'Pengajuan'),tanggal_pengajuan DESC")
            ->paginate($per_page);

        $stats = DB::table('laporans')
        ->where('laporans.user_id', $user->id)
        ->select(
            DB::raw("SUM(CASE WHEN status = 'Pengajuan' THEN 1 ELSE 0 END) as pengajuan"),
            DB::raw("SUM(CASE WHEN status = 'Progress' THEN 1 ELSE 0 END) as progress"),
            DB::raw("SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai")
        )
        ->first();
        return response()->json([
            'data' => [
                'all_data' => $data->items(),
                'total' => $data->total()
            ],
            'statistics' => $stats
        ]);
    }

    public function tanganiLaporan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'kategori' => 'required|string',
            'tanggal_selesai' => 'required|date',
            'deskripsi_penanganan' => 'required|string',
        ], [
            'status.required' => 'Silakan ubah status laporan terlebih dahulu.',
            'kategori.required' => 'Kategori laporan harus ditentukan.',
            'tanggal_selesai.required' => 'Tanggal selesai/Perkiraan selesai wajib diisi.',
            'deskripsi_penanganan.required' => 'Deskripsi penanganan tidak boleh kosong.',
        ]);

        // Jika gagal validasi, kembalikan semua pesan error
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $laporan = DB::table('laporans')->where('id', $id)->first();
        $tanggal_pengajuan = $laporan->tanggal_pengajuan;

        if ($laporan->status === $validated['status']) {
            return response()->json([
                'message' => 'Status laporan belum berubah'
            ], 400);
        }

        $estimasi = abs(\Carbon\Carbon::parse($validated['tanggal_selesai'])->diffInDays(\Carbon\Carbon::parse($tanggal_pengajuan)));

        DB::table('laporans')->where('id', $id)->update([
            'status' => $validated['status'],
            'kategori' => $validated['kategori'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'penyelesaian' => $validated['deskripsi_penanganan'],
            'estimasi' => $estimasi,
        ]);

          // Update tanggal status jika belum diisi
        if (strtolower($validated['status']) === 'progress') {
            DB::table('status_laporans')->where('laporan_id', $id)->update([
                'progress_tanggal' => \Carbon\Carbon::now()->format('Y-m-d'),
            ]);
        }

        if (strtolower($validated['status']) === 'selesai') {
            DB::table('status_laporans')->where('laporan_id', $id)->update([
                'selesai_tanggal' => \Carbon\Carbon::now()->format('Y-m-d'),
            ]);
        }

        $user = DB::table('users')->where('id', $laporan->user_id)->first();

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
        } else if(strtolower($validated['status']) === 'progress') {
            Mail::to($user->email)->send(new SendEmail($emailData));
        }

        return response()->json([
            'message' => 'Laporan berhasil diperbarui'
        ], 200);
    }

    public function detailLaporan(Request $request)
    {
        $laporan = DB::table('laporans')
            ->where('resi', '=', $request->resi)
            ->first();

        if (!$laporan) {
            abort(404, 'Laporan tidak ditemukan');
        }

        // Ambil data status proses dari tabel status_laporans
        $statusLaporan = DB::table('status_laporans')
            ->where('laporan_id', $laporan->id)
            ->first();

        return view('detail-laporan', [
            'laporan' => $laporan,
            'statusLaporan' => $statusLaporan
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

    // Public
    public function publicSearchFilter(Request $request) {
        return $this->searchAndFilter($request, 'public');
    }

    // Admin
    public function adminSearchFilter(Request $request) {
        return $this->searchAndFilter($request, 'admin');
    }

    // User
    public function userSearchFilter(Request $request) {
        return $this->searchAndFilter($request, 'user');
    }
}

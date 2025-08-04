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
        $data = DB::table('laporans')
            ->join('users', 'laporans.user_id', '=', 'users.id')
            ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi',
                'users.jenis_instansi as user_jenis_instansi'
            )
            ->orderByRaw("FIELD(laporans.status, 'Pengajuan', 'Progress', 'Selesai'),laporans.tanggal_pengajuan ASC")->get();

        return response()->json($data);
    }

    public function getPublicData(){
        $data = DB::table('laporans')
            ->select(
                'laporans.resi as resi',
                'laporans.judul_masalah as masalah',
                'laporans.tanggal_pengajuan as tanggal_pengajuan',
                'laporans.status as status',
                'laporans.lampiran as lampiran'
            )
            ->orderByRaw("laporans.tanggal_pengajuan DESC")
            ->get()
            ->map(function($item){
                $item->lampiran_url = $item->lampiran ? asset('storage/' . $item->lampiran) : null;
                return $item;
            });

        return response()->json($data);
    }

    public function getUserData(Request $request){
        $user = Auth::user();
        $data = DB::table('laporans')
            ->select('id', 'resi', 'judul_masalah', 'status', 'kategori', 'tanggal_pengajuan', 'tanggal_selesai', 'estimasi', 'deskripsi', 'lampiran')
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(status, 'Selesai', 'Progress', 'Pengajuan'),tanggal_pengajuan DESC")
            ->get();

        return response()->json($data);
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
}

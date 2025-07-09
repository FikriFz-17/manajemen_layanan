<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Mail\sendSelesaiEmail;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LaporanController extends Controller
{
    public function createlaporan(Request $request){
        $user = Auth::user();
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'masalah' => 'required|string',
            'deskripsi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,png,jpeg,docx|max:2048'
        ], [
            'tanggal.required' => 'Tanggal tidak boleh kosong',
            'masalah.required' => 'Masalah tidak boleh kosong',
            'deskripsi.required' => 'Deskripsi tidak boleh kosong',
            'lampiran.mimes' => 'Lampiran harus berupa PDF, JPG, PNG, DOC, atau DOCX',
            'lampiran.max' => 'Lampiran maksimal 2MB',
        ]);

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
            $originalName = $file->getClientOriginalName(); // nama asli file
            $lampiranPath = $file->storeAs('lampiran', $originalName, 'public');
        }

        DB::table('laporans')->insert([
            'user_id' => $user->id,
            'resi' => $resi,
            'judul_masalah' => $validated['masalah'],
            'status' => 'Pengajuan',
            'tanggal_pengajuan' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'lampiran' => $lampiranPath
        ]);

        return back()->with('success', 'Laporan berhasil diajukan, Pantau status laporan Anda di dashboard');
    }

    public function getAllData(Request $request){
        $data = DB::table('laporans')
            ->join('users', 'laporans.user_id', '=', 'users.id')
            ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi'
            )
            ->orderByRaw("FIELD(laporans.status, 'Pengajuan', 'Progress', 'Selesai'),laporans.tanggal_pengajuan ASC")->get();

        return response()->json($data);
    }

    public function getUserData(Request $request){
        $user = Auth::user();
        $data = DB::table('laporans')
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(status, 'Selesai', 'Progress', 'Pengajuan'),tanggal_pengajuan DESC")
            ->get();

        return response()->json($data);
    }

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
            return back()->withErrors(['Status laporan belum diubah. Silakan ubah status sebelum menyimpan.'])->withInput();
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

        return back()->with('success', "Laporan berhasil diproses dan notifikasi telah dikirim.");
    }
}

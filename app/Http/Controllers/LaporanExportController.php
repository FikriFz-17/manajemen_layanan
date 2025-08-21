<?php

namespace App\Http\Controllers;

use App\Exports\LaporansExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanExportController extends Controller
{
    public function exportAll(){
        $rawdata = DB::table('laporans')
            ->join('users', 'laporans.user_id', '=', 'users.id')
            ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi'
            )
            ->orderByRaw("laporans.tanggal_pengajuan ASC")->get();

        $data = $rawdata->map(function($item){
            return [
                'resi' => $item->resi,
                'user_nama'=>$item->user_nama,
                'user_email' => $item->user_email,
                'judul_masalah' => $item->judul_masalah,
                'tanggal_pengajuan' => $item->tanggal_pengajuan,
                'deskripsi' => $item->deskripsi,
            ];
        })->toArray();

        return Excel::download(new LaporansExport($data), 'SemuaLaporan.xlsx');
    }

    public function exportPerTahun(Request $request){
        $tahun = $request->tahun;
        if (!$tahun){
            return redirect()->back()->with('error', 'Data tidak tersedia');
        }

        $rawData = DB::table('laporans')
        ->join('users', 'laporans.user_id', '=', 'users.id')
        ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi'
        )
        ->whereYear('laporans.tanggal_pengajuan', $tahun)
        ->orderByRaw("laporans.tanggal_pengajuan ASC")
        ->get();

        $data = $rawData->map(function($item){
            return [
                'resi' => $item->resi,
                'user_nama'=>$item->user_nama,
                'user_email' => $item->user_email,
                'judul_masalah' => $item->judul_masalah,
                'tanggal_pengajuan' => $item->tanggal_pengajuan,
                'deskripsi' => $item->deskripsi,
            ];
        })->toArray();

        return Excel::download(new LaporansExport($data), "LaporanPerTahun_{$tahun}.xlsx");
    }

    public function exportPerBulan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if (!$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Data tidak tersedia');
        }

        $rawData = DB::table('laporans')
            ->join('users', 'laporans.user_id', '=', 'users.id')
            ->select(
                'laporans.*',
                'users.nama as user_nama',
                'users.email as user_email',
                'users.instansi as user_instansi'
            )
            ->whereMonth('laporans.tanggal_pengajuan', '=', $bulan)
            ->whereYear('laporans.tanggal_pengajuan', '=', $tahun)
            ->orderByRaw("laporans.tanggal_pengajuan ASC")
            ->get();

        if ($rawData->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pada bulan dan tahun tersebut.');
        }

        $data = $rawData->map(function($item){
            return [
                'resi' => $item->resi,
                'user_nama'=>$item->user_nama,
                'user_email' => $item->user_email,
                'judul_masalah' => $item->judul_masalah,
                'tanggal_pengajuan' => $item->tanggal_pengajuan,
                'deskripsi' => $item->deskripsi,
            ];
        })->toArray();

        return Excel::download(new LaporansExport($data), "laporan_{$tahun}_{$bulan}.xlsx");
    }

}

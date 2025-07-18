<?php

namespace App\Http\Controllers;

use App\Imports\DesaImport;
use App\Imports\KecamatanImport;
use App\Imports\PemdaImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function importKecamatan(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new KecamatanImport, $request->file('file'));

        return back()->with('success', 'Data kecamatan berhasil diimport');
    }

    public function importDesa(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new DesaImport, $request->file('file'));

        return back()->with('success', 'Data desa berhasil diimport');
    }

    public function importPemda(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new PemdaImport, $request->file('file'));

        return back()->with('success', 'Data pemda berhasil diimport');
    }
}

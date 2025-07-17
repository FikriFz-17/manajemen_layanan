<?php

namespace App\Imports;

use App\Models\Kecamatan;
use Maatwebsite\Excel\Concerns\ToModel;

class KecamatanImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Kecamatan([
            'id' => $row[0],
            'nama' => $row[1],
        ]);
    }
}

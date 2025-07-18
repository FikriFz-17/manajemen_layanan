<?php

namespace App\Imports;

use App\Models\Pemda;
use Maatwebsite\Excel\Concerns\ToModel;

class PemdaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Pemda([
            'nama' => $row[0],
        ]);
    }
}

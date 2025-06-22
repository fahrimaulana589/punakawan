<?php

namespace App\Imports;

use App\Models\GajiKaryawan;
use App\Models\Penggajian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PenggajianKaryawanImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Penggajian([
            'karyawan_id' => $row[0],
            'nama' => $row[1],
            'type' => $row[2],
            'total' => $row[3],
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

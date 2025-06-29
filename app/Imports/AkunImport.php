<?php

namespace App\Imports;

use App\Models\Akun;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AkunImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Lewati baris kosong (semua kolom kosong)
        if (collect($row)->filter()->isEmpty()) {
            return null;
        }

        $lastId = Akun::max('id') ?? 0;
        $kode = 'AKN' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        return new Akun([
            'id' => $row[0],
            'kode' => $kode,
            'nama' => $row[1],
            'tipe' => $row[2] ? $row[2] : '',
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

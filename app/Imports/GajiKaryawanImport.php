<?php

namespace App\Imports;

use App\Models\GajiKaryawan;
use App\Models\Jurnal;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class GajiKaryawanImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $gaji = Jurnal::where('id', $row[0])
            ->where('tipe', 2)
            ->first();
        $pegawai = Pegawai::find($row[1]);

        return new GajiKaryawan([
            'tanggal' => $gaji->tanggal,
            'pegawai_id' => $row[1],
            'gaji_id' => $gaji->id,
            'total' => $row[2],
            'gaji_pokok' => $pegawai->gaji,
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }

    
}

<?php

namespace App\Imports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KaryawanImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $lastId = Pegawai::max('id') ?? 0;
        $kode = 'KRY' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        
        return new Pegawai( [
            'nama' => $row[1], // Asumsi kolom pertama adalah nama
            'jabatan' => $row[2], // Asumsi kolom kedua adalah jabatan
            'alamat' => $row[3], // Asumsi kolom ketiga adalah alamat
            'no_hp' => $row[4], // Asumsi kolom keempat adalah no_hp
            'jenis_kelamin' => $row[5], // Asumsi kolom kelima adalah jenis_kelamin
            'gaji' => $row[6], // Asumsi kolom keenam adalah gaji
            'kode' => $kode,
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }

}

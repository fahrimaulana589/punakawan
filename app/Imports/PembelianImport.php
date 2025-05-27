<?php

namespace App\Imports;

use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PembelianImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $tanggal = null;
        if (is_numeric($row[1])) {
            $tanggal = Date::excelToDateTimeObject($row[1])->format('Y-m-d');
        }

        return new Jurnal([
            'tanggal' => $tanggal, // Asumsi kolom pertama adalah tanggal
            'nama' => $row[2], // Asumsi kolom keenam adalah nama
            'total' => $row[3], // Asumsi kolom kedua adalah total
            'pegawai_id' => $row[4], // Asumsi kolom ketiga adalah pegawai_id
            'debet_id' => $row[5], // Asumsi kolom keempat adalah debet_id
            'kredit_id' => $row[6], // Asumsi kolom kelima adalah kredit_id
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

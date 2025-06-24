<?php

namespace App\Imports;

use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JurnalImport implements ToModel,WithStartRow
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
        
        $tanggal = null;
        if (is_numeric($row[1])) {
            $tanggal = Date::excelToDateTimeObject($row[1])->format('Y-m-d');
        }

        return new Jurnal([
            'tanggal' => $tanggal, // Asumsi kolom pertama adalah tanggal
            'nama' => $row[2], // Asumsi kolom kedua adalah nama
            'total' => $row[3], // Asumsi kolom ketiga adalah total
            'debet_id' => $row[5], // Asumsi kolom keempat adalah debet_id
            'kredit_id' => $row[6], // Asumsi kolom kelima adalah kredit_id
            'tipe' => 1,
            'karyawan_id' => $row[4]
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai membaca dari baris kedua
    }
}

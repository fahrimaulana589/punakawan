<?php

namespace App\Imports;

use App\Models\Jurnal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BiayaImport implements ToModel,WithStartRow
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
            'nama' => $row[2], // Asumsi kolom kedua adalah nama
            'total' => $row[3], // Asumsi kolom ketiga adalah total
            'debet_id' => $row[5], // Asumsi kolom keempat adalah debet_id
            'kredit_id' => $row[6], // Asumsi kolom kelima adalah kredit_id
            'tipe' => 3,
            'pegawai_id' => $row[4]
        ]);
    }

    /**
     * Specify the row at which the import should start.
     *
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Change this to the row number you want to start from
    }
}

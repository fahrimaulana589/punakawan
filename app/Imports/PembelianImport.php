<?php

namespace App\Imports;

use App\Models\Belanja;
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
        // Lewati baris kosong (semua kolom kosong)
        if (collect($row)->filter()->isEmpty()) {
            return null;
        }
        
        $tanggal = null;
        if (is_numeric($row[1])) {
            $tanggal = Date::excelToDateTimeObject($row[1])->format('Y-m-d');
        }

        return new Belanja([
            'tanggal' => $tanggal, // Asumsi kolom pertama adalah tanggal
            'total' => $row[3], // Asumsi kolom kedua adalah total
            'karyawan_id' => $row[4], // Asumsi kolom ketiga adalah karyawan_id
            'konsumsi_id' => $row[5], // Asumsi kolom keempat adalah debet_id
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

<?php

namespace App\Imports;

use App\Models\Peralatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PeralatanImport implements ToModel,WithStartRow
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
        
        $tanggal_aktif = null;
        if (is_numeric($row[2])) {
            $tanggal_aktif = Date::excelToDateTimeObject($row[2])->format('Y-m-d');
        }

        $tanggal_nonaktif = null;
        if (is_numeric($row[3])) {
            $tanggal_nonaktif = Date::excelToDateTimeObject($row[3])->format('Y-m-d');
        }

        return new Peralatan([
            'nama' => $row[1],
            'tanggal_aktif' => $tanggal_aktif,
            'tanggal_nonaktif' => $tanggal_nonaktif,
            'harga' => $row[4],
            'umur_ekonomis' => $row[5],
            'nilai_sisa' => $row[6],
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

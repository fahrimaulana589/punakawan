<?php

namespace App\Imports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AbsensiImport implements ToModel, WithStartRow
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

        return new Absensi([
            'tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]),
            'karyawan_id' => $row[1],
            'status' => $row[2],
            'alasan' => $row[3] ?? null,
        ]);
    }
    public function startRow(): int
    {
        return 2; // Assuming the first row is the header
    }
}

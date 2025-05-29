<?php

namespace App\Imports;

use App\Models\Persedian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PersedianImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Persedian([
            'tahun' => $row[2],
            'bulan' => $row[3],
            'konsumsi_id' => $row[4],
            'total' => $row[5],
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

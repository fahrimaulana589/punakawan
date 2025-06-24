<?php

namespace App\Imports;

use App\Models\Profile;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProfileImport implements ToModel, WithStartRow
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
        
        return new Profile([
            'nama' => $row[0],
            'alamat' => $row[1],
            'handphone' => $row[2]
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}

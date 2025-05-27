<?php

namespace App\Imports;

use App\Models\Konsumsi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BahanProduksiImport implements ToModel,WithStartRow
{

    public function model(array $row){
        
        return new Konsumsi([
            'debet_id' => $row[2], // Asumsi kolom pertama adalah debet_id
            'kredit_id' => $row[3], // Asumsi kolom kedua adalah kredit_id
            'nama' => $row[1], // Asumsi kolom ketiga adalah nama
        ]);
    }

    public function startRow(): int{
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

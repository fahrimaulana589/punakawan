<?php

namespace App\Imports;

use App\Models\PersediaanProdukJadi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PersedianProdukImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PersediaanProdukJadi([
            'tahun' => $row[3],
            'bulan'  => $row[4],
            'stok'  => $row[1],
            'stok_sisa'  => $row[2],
            'produk_id' => $row[0]
        ]);
    }

    /**
    * Specify the row at which the import should start.
    *
    * @return int
    */
    public function startRow(): int
    {
        return 2;
    }
}

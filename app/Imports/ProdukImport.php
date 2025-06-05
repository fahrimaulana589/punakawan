<?php

namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProdukImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $lastId = Produk::max('id') ?? 0;
        $kode = 'PRD' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
       
        $produk = Produk::create([
            'kode' => $kode,
            'nama' => $row[1],
            'harga' => $row[2],
            'stok' => $row[3],
            'status' => $row[5],
        ]);

        // Coba decode string sebagai JSON
        $decoded = json_decode($row[4], true);
       
        //Cek apakah hasil decode adalah array
        if (is_array($decoded)) {
            $produk->parent()->attach($decoded);
        }

        return null;
    }
    public function startRow(): int{
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

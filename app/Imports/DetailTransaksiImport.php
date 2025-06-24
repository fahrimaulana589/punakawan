<?php

namespace App\Imports;

use App\Models\Penjualan;
use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DetailTransaksiImport implements ToModel, WithStartRow
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
        
        $produk = Produk::find($row[1]);
        if($produk){
        return new Penjualan([
                    'produk_id' => $produk->id,
                    'jumlah' => $row[2],
                    'harga' => $produk->harga,
                    'total' => $produk->harga * $row[2],
                    'transaksi_id' => $row[0],
                ]);
        }
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'akun' => new AkunImport(),
            'produk' => new ProdukImport(),
            'bahan produksi' => new BahanProduksiImport(),
            'karyawan' => new KaryawanImport(),
            'user' => new UserImport(),
            'transaksi' => new TransaksiImport(),
            'pembelian' => new PembelianImport(),
            'gaji' => new GajiImport(),
            'persedian' => new PersedianImport(),
            'peralatan' => new PeralatanImport(),
            'jurnal' => new JurnalImport(),
            'profile' => new ProfileImport(),
            'absensi' => new AbsensiImport(),
        ];
    }
}

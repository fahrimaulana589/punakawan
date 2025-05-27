<?php

namespace App\Imports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TransaksiImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $tanggal = null;
        if (is_numeric($row[1])) {
            $tanggal = Date::excelToDateTimeObject($row[1])->format('Y-m-d');
        }

        $lastId = Transaksi::max('id') ?? 0;
        $kode = 'TSK' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);


        // dump($tanggal);
        // return null;
        return new Transaksi([
            'tanggal' => $tanggal, // Asumsi kolom pertama adalah tanggal
            'total' => $row[2], // Asumsi kolom kedua adalah total
            'pegawai_id' => $row[3], // Asumsi kolom ketiga adalah pegawai_id
            'status' => $row[4], // Asumsi kolom keempat adalah status,
            'debet_id' => $row[5], // Asumsi kolom kelima adalah debet_id
            'kredit_id' => $row[6], // Asumsi kolom keenam adalah kredit_id
            'kode' => $kode, // Kode transaksi yang dihasilkan
        ]);
        
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

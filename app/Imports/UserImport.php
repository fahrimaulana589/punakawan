<?php

namespace App\Imports;

use App\Models\User;
use Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class UserImport implements ToModel,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row[1], // Asumsi kolom pertama adalah nama
            'email' => $row[2], // Asumsi kolom kedua adalah email
            'password' => Hash::make($row[3]), // Asumsi kolom ketiga adalah password, dienkripsi
            'karyawan_id' => $row[4], // Asumsi kolom keempat adalah karyawan_id
        ]);

        $user->assignRole($row[5]); // Asumsi kolom kelima adalah role

        return null;
    }

    public function startRow(): int
    {
        return 2; // Mulai dari baris kedua, karena baris pertama biasanya header
    }
}

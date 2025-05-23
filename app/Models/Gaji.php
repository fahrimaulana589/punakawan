<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'nama',
        'total'
    ];

    public function karyawans(){

        return $this->hasMany(GajiKaryawan::class);
    }
}

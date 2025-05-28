<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'nama',
        'total',
        'debet_id',
        'kredit_id',
    ];

    public function karyawans(){

        return $this->hasMany(GajiKaryawan::class);
    }

    public function totalRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['total'];

                return format_uang($nilai);
            },
        );
    }

    public function tanggalFormat(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['tanggal'];

                return format_tanggal($nilai);
            },
        );
    }
}

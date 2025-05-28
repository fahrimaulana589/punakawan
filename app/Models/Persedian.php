<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persedian extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'total',
        'konsumsi_id'
    ];

    public function bahanProduksi(){

        return $this->belongsTo(Konsumsi::class,'konsumsi_id');
    }

    public function namaBulan(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $bulan = $data['bulan'];

                $daftarBulan = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];

                return $daftarBulan[$bulan] ?? 'Tidak Diketahui';
            },
        );
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
}

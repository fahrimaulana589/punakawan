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

                return nama_bulan($bulan);
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

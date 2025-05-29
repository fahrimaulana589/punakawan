<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
    ];

    public function namaBulan(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $bulan = $data['bulan'];

                return nama_bulan($bulan);
            },
        );
    }
}

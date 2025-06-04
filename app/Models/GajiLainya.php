<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GajiLainya extends Model
{
    use HasFactory;

    protected $fillable = [
        'gaji_karyawan_id',
        "type",
        "nama",
        "total",
        'lainya_pokok'
    ];

    public function totalRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['total'];

                return format_uang($nilai);
            },
        );
    }
    public function lainyaPokokRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['lainya_pokok'];

                return format_uang($nilai);
            },
        );
    }
}

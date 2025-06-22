<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Penggajian extends Model
{
    use HasFactory;

    protected $fillable = [
        "type",
        "nama",
        "total",
        'karyawan_id'
    ];

    public function karyawan(){
        return $this->belongsTo(Karyawan::class);
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

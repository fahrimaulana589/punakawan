<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class   GajiKaryawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'pegawai_id',
        'gaji_id',
        'total',
        'gaji_pokok'
    ];

    public function gajiLainyas(){
        return $this->hasMany(GajiLainya::class);
    }

    public function karyawan(){
        return $this->belongsTo(Pegawai::class,'pegawai_id');
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

    public function gajiRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['gaji_pokok'];

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

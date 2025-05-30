<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Belanja extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'total',
        'konsumsi_id',
        'pegawai_id'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
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

    public function bahanProduksi(){

        return $this->belongsTo(Konsumsi::class,'konsumsi_id');
    }
}

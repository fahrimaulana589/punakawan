<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiKaryawan extends Model
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Jurnal extends Model
{
    use HasFactory;

    protected $fillable = [
        'debet_id',
        'kredit_id',
        'nama',
        'total',
        'tanggal',
        'tipe',
        'karyawan_id'
    ];
    public function debet()
    {
        return $this->belongsTo(Akun::class, 'debet_id');
    }
    public function kredit()
    {
        return $this->belongsTo(Akun::class, 'kredit_id');
    }
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
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

    public function karyawans(){

        return $this->hasMany(GajiKaryawan::class,'gaji_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'debet_id',
        'kredit_id',
        'karyawan_id',
        'tanggal',
        'total',
        'status',
        'kode',
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

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

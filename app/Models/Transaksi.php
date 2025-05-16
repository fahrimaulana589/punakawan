<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'debet_id',
        'kredit_id',
        'pegawai_id',
        'tanggal',
        'total',
        'status',
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
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
    public function getTotalAttribute($value)
    {
        return number_format($value, 0, ',', '.');
    }
    public function getTanggalAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }

}

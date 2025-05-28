<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'jumlah',
        'harga',
        'total',
        'transaksi_id',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
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

    public function hargaRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['harga'];

                return format_uang($nilai);
            },
        );
    }
}

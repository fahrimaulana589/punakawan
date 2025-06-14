<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PersediaanProdukJadi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'stok',
        'stok_sisa',
        'produk_id'
    ];

    public function produk(){

        return $this->belongsTo(Produk::class,'produk_id');
    }

    public function stokSisaProduk(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $produk = $this->produk;
                $produk->setPeriode($this->tahun,$this->bulan);

                return $produk->stokSisa;
            },
        );
    }

    public function stokTerjualProduk(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $produk = $this->produk;
                $produk->setPeriode($this->tahun,$this->bulan);

                return $produk->stokTerjual;
            },
        );
    }

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

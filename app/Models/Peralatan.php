<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peralatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal_aktif',
        'tanggal_nonaktif',
        'harga',
        'umur_ekonomis',
        'nilai_sisa',
    ];

    public function status(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $tanggal = $data['tanggal_nonaktif'];

                return $tanggal ? format_tanggal($tanggal)  : 'Masih Terpakai';
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

    public function bebanPenyusutan(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $peralatan = $this;

                return (int) round(($peralatan->harga - $peralatan->nilai_sisa) / $peralatan->umur_ekonomis);
            },
        );
    }        

    public function nilaisisaRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['nilai_sisa'];

                return format_uang($nilai);
            },
        );
    }

    public function umurekonomisBulan(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['umur_ekonomis'];

                return $nilai." Bulan";
            },
        );
    }

    public function tanggalaktifFormat(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['tanggal_aktif'];

                return format_tanggal($nilai);
            },
        );
    }
    
}

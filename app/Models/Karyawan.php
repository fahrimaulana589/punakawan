<?php

namespace App\Models;
    
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jabatan',
        'alamat',
        'no_hp',
        'jenis_kelamin',
        'gaji', 
        'kode'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function penggajians(){

        return $this->hasMany(Penggajian::class);
    }

    public function gajiRupiah(): Attribute
    {
        return Attribute::make(
            get: function ($key,$data) {
                $nilai = $data['gaji'];

                return format_uang($nilai);
            },
        );
    }
}

<?php

namespace App\Models;
    
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
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
}

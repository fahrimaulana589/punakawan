<?php

namespace App\Models;

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
}

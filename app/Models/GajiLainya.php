<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiLainya extends Model
{
    use HasFactory;

    protected $fillable = [
        'penggajian_id',
        'gaji_karyawan_id',
        'total',
    ];
}

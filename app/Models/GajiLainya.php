<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiLainya extends Model
{
    use HasFactory;

    protected $fillable = [
        'gaji_karyawan_id',
        "type",
        "nama",
        "total",    
    ];
}

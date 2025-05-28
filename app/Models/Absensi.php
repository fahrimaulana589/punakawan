<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'pegawai_id',
        'status',
        'alasan'
    ];

    public function pegawai(){

        return $this->belongsTo(Pegawai::class);
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

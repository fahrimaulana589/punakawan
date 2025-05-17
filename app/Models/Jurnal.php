<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $fillable = [
        'debet_id',
        'kredit_id',
        'nama',
        'total',
        'tanggal',
        'pegawai_id'
    ];
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
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}

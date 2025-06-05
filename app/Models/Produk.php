<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'harga',
        'kode',
        'stok',
        'status',
    ];

    // App\Models\Produk.php

    public function parent()
    {
        return $this->belongsToMany(Produk::class, 'produk_to_parent', 'produk_id', 'parent_id')->withPivot('jumlah');
    }

    public function children()
    {
        return $this->belongsToMany(Produk::class, "produk_to_parent", 'parent_id', 'produk_id')->withPivot('jumlah');
    }


    public function tipe(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->children()->exists()) {
                    return 'induk';
                } elseif ($this->parent()->exists()) {
                    return 'paket';
                } else {
                    return 'tunggal';
                }
            },
        );
    }

    public function stok(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (in_array($this->tipe, ['tunggal', 'induk'])) {
                    return $this->attributes['stok']; // langsung ambil dari DB
                }
    
                if ($this->tipe === 'paket') {
                    // Load children kalau belum dimuat
                    if (!$this->relationLoaded('parent')) {
                        $this->load('parent');
                    }
    
                    if ($this->parent->isEmpty()) {
                        return 0;
                    }
    
                    // Hitung stok minimum berdasarkan kebutuhan masing-masing child
                    $stokPerKomponen = $this->parent->map(function ($produk) {
                        $stokAsli = $produk->stok; // ini bisa juga akses accessor stok dari child
                        $jumlahDibutuhkan = $produk->pivot->jumlah ?: 1;
                        return floor($stokAsli / $jumlahDibutuhkan);
                    });
    
                    return $stokPerKomponen->min(); // ambil nilai terendah sebagai stok paket
                }
    
                return 0;
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

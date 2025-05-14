<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Produk;

class FilterProdukInduk
{
    public function handle(Request $request, Closure $next)
    {
        $produkId = $request->route('id'); // asumsi :route param-nya 'id'
        $produk = Produk::find($produkId);

        if (!$produk) {
            abort(404, 'Produk tidak ditemukan.');
        }

        if ($produk->tipe === 'induk') {
            abort(403, 'Produk induk tidak bisa dipaketkan.');
        }

        return $next($request);
    }
}

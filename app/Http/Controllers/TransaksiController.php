<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psy\Util\Json;
use RealRashid\SweetAlert\Facades\Alert;
use function GuzzleHttp\json_encode;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Response;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Transaksi::query();
        $query->where('status', '');
        $transaksis = filter($query);
        return view('transaksi.index',compact('transaksis'));
    }

     /**
     * Display a listing of the resource.
     */
    public function void()
    {
        $query = Transaksi::query();
        $query->where('status', 'batal');
        $transaksis = filter($query);
        return view('transaksi.void',compact('transaksis'));
    }

     /**
     * Display a listing of the resource.
     */
    public function riwayat()
    {
        $query = Transaksi::query();
        $query->where('status', 'selesai');
        $transaksis = filter($query);
        return view('transaksi.riwayat',compact('transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ismanual = \Route::currentRouteName() === 'penjualan.create.manual';

        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldProdukIds = old('produk_ids', [null]);  // Default produk_id null
        $oldJumlahs = old('jumlahs', [1]);  // Default jumlah 1

        // Gabungkan produk_id dan jumlah ke dalam format yang diinginkan
        $old = array_map(function ($produkId, $jumlah) {
            return ['produk_id' => $produkId, 'jumlah' => $jumlah];
        }, $oldProdukIds, $oldJumlahs);

        $old = json_encode($old); // hasil asli: [{"produk_id":null,"jumlah":1}]

        $errors = session('errors');
        $messages = $errors ? $errors->messages() : [];

        $messages = json_encode($messages); // hasil asli: {"produk_ids":["The produk ids field is required."]}

        $produks = Produk::with(['parent' => function ($q) {
            $q->withPivot('jumlah'); // ini penting
        },'children' => function ($q) {
            $q->withPivot('jumlah'); // ini penting
        }])->get();

        if ($ismanual) {
            return view('transaksi.manual', compact('produks','old','messages'));
        }

        return view('transaksi.create', compact('produks','old','messages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tanggal = now();
        $status = '';
        $ismanual = url()->previous() === route('penjualan.create.manual');
        if ($ismanual) {
            $request->validate([
                'tanggal'   => ['required', 'before_or_equal:now', 'date'],
            ]);
            $tanggal = $request->tanggal;
            $status = 'selesai';
        }
        $request->validate([
            'produk_ids'   => ['required', 'array', 'min:1'],
            'produk_ids.*' => ['required', 'exists:produks,id'],
            'jumlahs'      => ['required', 'array'],
            'jumlahs.*'    => ['required', 'integer', 'min:1'],
        ]);

        $pegawai = auth()->user()->pegawai;

        $total = 0;
        foreach ($request->produk_ids as $key => $produkId) {
            $produk = Produk::find($produkId);
            $jumlah = $request->jumlahs[$key];

            // Hitung total untuk setiap produk
            $total += $produk->harga * $jumlah;
        }

        $produkIds = $request->produk_ids;
        $jumlahs   = $request->jumlahs;

        $produks = Produk::with(['parent', 'children'])->get();

        // Buat array ID dan stok
        $stokMap = $produks->pluck('stok', 'id')->toArray(); // [produk_id => stok]
        
        $errors = [];
        $produkdanstok = [];
        foreach ($produkIds as $i => $produkId) {
            $jumlah = $jumlahs[$i];
        
            $produk = $produks->firstWhere('id', $produkId);
            
            if(!$produk->parent->isEmpty()){
                foreach ($produk->parent as $parent) {
                    //ambil jumlah yang dibutuhkan jumlah * jumlah yang di pivot
                    $jumlahDibutuhkan = $jumlah * $parent->pivot->jumlah;
                    $stokMap[$parent->id] -= $jumlahDibutuhkan;
                    $produkdanstok[$parent->id] = $stokMap[$parent->id];
                
                    if ($stokMap[$parent->id] < 0) {
                        $errors["produk_ids.$i"] = "Stok produk '{$produk->nama}' tidak mencukupi.";
                        
                    }
                }
            }else{
                // Jika produk tidak memiliki parent, cukup periksa stoknya
                $stokMap[$produkId] -= $jumlah;
                $produkdanstok[$produkId] = $stokMap[$produkId];
                if ($stokMap[$produkId] < 0) {
                    $errors["produk_ids.$i"] = "Stok produk '{$produk->nama}' tidak mencukupi.";
                }                   
            }
        }
        // Jika ada error, lemparkan exception validasi
        if (!empty($errors) && !$ismanual) {
            throw ValidationException::withMessages($errors);
        }
        // Update total transaksi

        $lastId = Transaksi::max('id') ?? 0;
        $kode = 'TSK' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $transaksi = Transaksi::create([
            'debet_id'   => 7,
            'kredit_id'  => 1,
            'pegawai_id' => $pegawai->id,
            'tanggal'    => $tanggal,
            'total'      => $total,
            'kode'       => $kode,
            'status'     => $status
        ]);

        foreach ($request->produk_ids as $key => $produkId) {
            $produk = Produk::find($produkId);
            $jumlah = $request->jumlahs[$key];

            // Simpan penjualan
            Penjualan::create([
                'produk_id' => $produkId,
                'jumlah'    => $jumlah,
                'harga'     => $produk->harga,
                'total'     => $produk->harga * $jumlah,
                'transaksi_id' => $transaksi->id
            ]);
        }

        if (!empty($errors) && !$ismanual) {
            foreach ($produkdanstok as $key => $stok){
                $produk = Produk::find($key);
                $produk->stok = $stok;
                $produk->save();
            }
        }

        if($ismanual){
            return redirect()->route('penjualan.riwayat')->with('success', 'Transaksi created successfully.');
        }

        return redirect()->route('penjualan')->with('success', 'Transaksi created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['penjualan.produk', 'pegawai'])->findOrFail($id);
        $transaksi->tanggal = \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y');

        // dd($transaksi->penjualan->first()->produk->nama);
     
        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Display the specified resource.
     */
    public function struk($id)
    {
        $transaksi = Transaksi::with(['penjualan.produk', 'pegawai'])->findOrFail($id);
        $transaksi->tanggal = \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y');

        $html = view('transaksi.struk', compact('transaksi'))->render();

        $bodyHeight = Browsershot::html($html)
            ->setOption('width', '58mm')
            ->evaluate('document.body.scrollHeight');
        // dd($bodyHeight);

        // Generate PDF dan tampilkan langsung
        $pdf = Browsershot::html($html)
            ->setOption('width', '58mm')
            ->setOption('height', $bodyHeight.'px')
            ->pdf(); // return binary content

        // Kirim ke browser
        return Response::make($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="struk.pdf"',
        ]);

        // return view('transaksi.struk', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function cancel(Transaksi $id)
    {
        $produkdanstok = [];
        foreach ($id->penjualan as $i => $penjualan) {
            $jumlah = $penjualan->jumlah;
        
            $produk = $penjualan->produk;
            
            if(!$produk->parent->isEmpty()){
                foreach ($produk->parent as $parent) {
                    //ambil jumlah yang dibutuhkan jumlah * jumlah yang di pivot
                    $jumlahDibutuhkan = $jumlah * $parent->pivot->jumlah;
                    $produkdanstok[$parent->id] = !isset($produkdanstok[$parent->id]) ? 0 : $produkdanstok[$parent->id];
                    $produkdanstok[$parent->id] += $jumlahDibutuhkan;
                }
            }else{
                // Jika produk tidak memiliki parent, cukup periksa stoknya
                $produkdanstok[$produk->id] = !isset($produkdanstok[$produk->id]) ? 0 : $produkdanstok[$produk->id];
                $produkdanstok[$produk->id] += $jumlah;                   
            }
        }

        foreach ($produkdanstok as $key => $stok){
            $produk = Produk::find($key);
            $produk->stok += $stok;
            $produk->save();
        }

        $id->status = 'batal';
        $id->save();

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function finish(Transaksi $id)
    {
        $id->status = 'selesai';
        $id->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function edit(Transaksi $id)
    {
        $transaksi = $id;

        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldProdukIds = old('produk_ids', [null]);  // Default produk_id null
        $oldJumlahs = old('jumlahs', [1]);  // Default jumlah 1

        // Gabungkan produk_id dan jumlah ke dalam format yang diinginkan
        $old = array_map(function ($produkId, $jumlah) {
            return ['produk_id' => $produkId, 'jumlah' => $jumlah];
        }, $oldProdukIds, $oldJumlahs);

        if($oldProdukIds == [null]){
            $old = [];
            foreach ($transaksi->penjualan as $penjualan) {
                $old[] = [
                    'produk_id' => $penjualan->produk_id,
                    'jumlah' => $penjualan->jumlah
                ];
            }
        }

        $old = json_encode($old); // hasil asli: [{"produk_id":null,"jumlah":1}]

        $errors = session('errors');
        $messages = $errors ? $errors->messages() : [];

        $messages = json_encode($messages); // hasil asli: {"produk_ids":["The produk ids field is required."]}

        $produks = Produk::with(['parent' => function ($q) {
            $q->withPivot('jumlah'); // ini penting
        },'children' => function ($q) {
            $q->withPivot('jumlah'); // ini penting
        }])->get();

        return view('transaksi.edit', compact('produks','old','messages','transaksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $id)
    {
        $transaksi = $id;

        $request->validate([
                'tanggal'   => ['required', 'before_or_equal:now', 'date'],
            ]);
            
        $tanggal = $request->tanggal;
        
        $request->validate([
            'produk_ids'   => ['required', 'array', 'min:1'],
            'produk_ids.*' => ['required', 'exists:produks,id'],
            'jumlahs'      => ['required', 'array'],
            'jumlahs.*'    => ['required', 'integer', 'min:1'],
        ]);

        $total = 0;
        foreach ($request->produk_ids as $key => $produkId) {
            $produk = Produk::find($produkId);
            $jumlah = $request->jumlahs[$key];

            // Hitung total untuk setiap produk
            $total += $produk->harga * $jumlah;
        }

        $transaksi->update([
            'tanggal'    => $tanggal,
            'total'      => $total,
        ]);

        $transaksi->penjualan()->delete(); // Hapus penjualan lama

        foreach ($request->produk_ids as $key => $produkId) {
            $produk = Produk::find($produkId);
            $jumlah = $request->jumlahs[$key];

            // Simpan penjualan
            Penjualan::create([
                'produk_id' => $produkId,
                'jumlah'    => $jumlah,
                'harga'     => $produk->harga,
                'total'     => $produk->harga * $jumlah,
                'transaksi_id' => $transaksi->id
            ]);
        }

        return back()->with('success', 'Transaksi updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $id)
    {

        $id->penjualan()->delete();

        //hapus transaski
        try {
            $id->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'Transaksi dealted successfully.');
    
    }
}

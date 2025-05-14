<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    public function index()
    {
        $title = 'Delete Produk!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $produks = Produk::paginate(10);
        return view('produk.index', compact('produks'));
    }

    public function create()
    {
        $lastId = Produk::max('id') ?? 0;
        $kode = 'PRD' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        return view('produk.create', compact('kode'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:produks,nama',
            'kode' => 'required|string|max:255|unique:produks,kode',
            'harga' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);

        Produk::create($request->all());

        return redirect()->route('produk')->with('success', 'Produk created successfully.');
    }
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);

        return view('produk.edit', compact('produk'));
    }

    public function paket($id)
    {
        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldProdukIds = old('produk_ids', [null]);  // Default produk_id null
        $oldJumlahs = old('jumlahs', [1]);  // Default jumlah 1

        // Gabungkan produk_id dan jumlah ke dalam format yang diinginkan
        $old = array_map(function ($produkId, $jumlah) {
            return ['produk_id' => $produkId, 'jumlah' => $jumlah];
        }, $oldProdukIds, $oldJumlahs);

        $errors = session('errors');
        $messages = $errors ? $errors->messages() : [];

        $messages = json_encode($messages); // hasil asli: {"produk_ids":["The produk ids field is required."]}

        $produk = Produk::findOrFail($id);

        // Jika tidak ada old (pertama kali buka halaman), ambil dari relasi children
        if (empty(old('produk_ids'))) {
            $old = [];
            foreach ($produk->parent as $parent) {
                $old[] = [
                    'produk_id' => $parent->id,
                    'jumlah' => $parent->pivot->jumlah ?? 1
                ];
            }
        }

        $old = array_map(function ($item) {
            return [
                'produk_id' => $item['produk_id'] ?? null,
                'jumlah' => $item['jumlah'] ?? 1
            ];
        }, $old);

        $old = json_encode($old); // hasil asli: [{"produk_id":null,"jumlah":1}]

        $produks = Produk::where('id', '!=', $produk->id)
            ->whereDoesntHave('parent')
            ->get();

        return view('produk.paket', compact('produk','produks', 'old', 'messages'));
    }

    public function storeToPaket(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        if(empty($request->produk_ids)){
            $produk->parent()->sync([]);

            return back()->with('success', 'Produk updated successfully.');
        };

        $request->validate([
            'produk_ids'   => ['required', 'array', 'min:1'],
            'produk_ids.*' => ['required', 'exists:produks,id'],
            'jumlahs'      => ['required', 'array'],
            'jumlahs.*'    => ['required', 'integer', 'min:1'],
        ]);

        // Validasi tidak boleh memasukkan produk bertipe 'paket'
        foreach ($request->produk_ids as $pid) {
            $item = Produk::find($pid);
            if ($item->tipe === 'paket') {
                return back()->withErrors(['produk_ids' => 'Produk paket tidak bisa dimasukkan ke dalam paket lain'])->withInput();
            }
        }

        $syncData = [];
        foreach ($request->produk_ids as $index => $childId) {
            $syncData[$childId] = ['jumlah' => $request->jumlahs[$index]];
        }

        $produk->parent()->sync($syncData);

        return back()->with('success', 'Produk updated successfully.');
    
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:produks,nama,'.$id,
            'harga' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
            'stok' => [
                'nullable',
                'integer',
                'min:0'
            ],
        ]);


        $produk = Produk::findOrFail($id);

        if($request->stok > 0){
           $stok = $produk->stok + $request->stok;            
           $request->merge(['stok' => $stok]);
        }else{
            $request->merge(['stok' => $produk->stok]);
        }       

        $produk->update($request->all());

        return back()->with('success', 'Produk updated successfully.');
    }
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produk')->with('success', 'Produk deleted successfully.');
    }
}

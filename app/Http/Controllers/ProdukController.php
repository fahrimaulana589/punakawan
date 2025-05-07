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
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:produks,nama,'.$id,
            'harga' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);
        
        $produk = Produk::findOrFail($id);
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

<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    public function index()
    {
        $title = 'Delete Akun!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $akuns = Akun::paginate(10);
        return view('akun.index', compact('akuns'));
    }

    public function create()
    {
        $lastId = Akun::max('id') ?? 0;
        $kode = 'AKN' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        return view('akun.create', compact('kode'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:akuns,nama',
            'kode' => 'required|string|max:255|unique:akuns,kode'
        ]);

        Akun::create($request->all());

        return redirect()->route('akun')->with('success', 'Akun created successfully.');
    }
    public function edit($id)
    {
        $akun = Akun::findOrFail($id);
        return view('akun.edit', compact('akun'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:akuns,nama,'.$id,
        ]);
        
        $akun = Akun::findOrFail($id);
        $akun->update($request->all());

        return back()->with('success', 'Akun updated successfully.');
    }
    public function destroy($id)
    {
        $akun = Akun::findOrFail($id);
        $akun->delete();

        return redirect()->route('akun')->with('success', 'Akun deleted successfully.');
    }
}

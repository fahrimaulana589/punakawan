<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index()
    {
        $title = 'Delete Pegawai!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $pegawais = Pegawai::paginate(10);
        return view('pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        $lastId = Pegawai::max('id') ?? 0;
        $kode = 'PGW' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        
        return view('pegawai.create',compact('kode'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawais,nama',
            'kode' => 'required|string|max:255|unique:pegawais,kode',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|numeric',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => ['required', 'string', Rule::in(['P', 'L'])],
            'gaji' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);
        

        Pegawai::create($request->all());

        return redirect()->route('pegawai')->with('success', 'Pegawai created successfully.');
    }
    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawais,nama,'.$id,
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|numeric',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => ['required', 'string', Rule::in(['P', 'L'])],
            'gaji' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
            ],
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->all());

        return back()->with('success', 'Pegawai updated successfully.');
    }
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai')->with('success', 'Pegawai deleted successfully.');
    }
}

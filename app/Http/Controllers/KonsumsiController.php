<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Konsumsi;
use Illuminate\Http\Request;

class KonsumsiController extends Controller
{
    public function index()
    {
        $title = 'Delete Bahan Produksi!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $konsumsis = Konsumsi::paginate(10);
        return view('konsumsi.index', compact('konsumsis'));
    }

    public function create()
    {

        $akuns = Akun::all();

        return view('konsumsi.create',compact('akuns'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:konsumsis,nama',
            'debet_id' => 'required|exists:akuns,id',
            'kredit_id' => 'required|exists:akuns,id',
        ]);

        Konsumsi::create($request->all());

        return redirect()->route('bahankonsumsi')->with('success', 'Bahan Produksi created successfully.');
    }
    public function edit($id)
    {
        $konsumsi = Konsumsi::findOrFail($id);
        $akuns = Akun::all();
        return view('konsumsi.edit', compact('konsumsi','akuns'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:konsumsis,nama,'.$id,
            'debet_id' => 'required|exists:akuns,id',
            'kredit_id' => 'required|exists:akuns,id',
        ]);
        
        $konsumsi = Konsumsi::findOrFail($id);
        $konsumsi->update($request->all());

        return back()->with('success', 'Bahan Produksi updated successfully.');
    }
    public function destroy($id)
    {
        $konsumsi = Konsumsi::findOrFail($id);
        $konsumsi->delete();

        return back()->with('success', 'Bahan Produksi deleted successfully.');
    }
}

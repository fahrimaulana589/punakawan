<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\Konsumsi;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Jurnal!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $jurnals = Jurnal::where('tipe','=',1)->paginate(10);
        return view('jurnal.index', compact('jurnals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $akuns = Akun::all();
        return view('jurnal.create', compact('akuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'nama' => 'required|string|max:255',
            'debet_id' => 'required|exists:akuns,id',
            'kredit_id' => 'required|exists:akuns,id',
            'total' => 'required|numeric|min:0',
        ]);

        $data =[
            'debet_id' => $request->debet_id, // Assuming 1 is the ID for the "Jurnal" account
            'kredit_id' => $request->kredit_id, // Assuming 2 is the ID for the "Kas" account
            'pegawai_id' => auth()->user()->pegawai_id,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'total' => $request->total,
        ];

        Jurnal::create($data);

        return redirect()->route('jurnal')->with('success', 'Jurnal created successfully.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jurnal = Jurnal::findOrFail($id);
        $akuns = Akun::all();
        return view('jurnal.edit', compact('jurnal','akuns'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'nama' => 'required|string|max:255',
            'debet_id' => 'required|exists:akuns,id',
            'kredit_id' => 'required|exists:akuns,id',
            'total' => 'required|numeric|min:0',
        ]);

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->update($request->all());
        return back()->with('success', 'Jurnal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jurnal = Jurnal::findOrFail($id);
        $jurnal->delete();

        return redirect()->route('jurnal')->with('success', 'Jurnal deleted successfully.');
    }
}

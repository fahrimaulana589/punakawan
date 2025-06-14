<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\Konsumsi;
use Illuminate\Http\Request;

class BiayaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Jurnal!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        // Filter Jurnal based on type
        $query = Jurnal::query();
        $query->where('tipe', '=', 3);

        $biayas = filter($query);
        return view('biaya.index', compact('biayas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $biayas = Akun::where("tipe","=","biaya")->get();
        return view('biaya.create', compact('biayas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'debet_id' => 'required|exists:akuns,id',
            'total' => 'required|numeric|min:0',
        ]);

        $akun = Akun::find($request->debet_id);

        $data =[
            'debet_id' => $request->debet_id, // Assuming 1 is the ID for the "Jurnal" account
            'kredit_id' => 1, // Assuming 2 is the ID for the "Kas" account
            'pegawai_id' => auth()->user()->pegawai_id,
            'tanggal' => $request->tanggal,
            'nama' => $akun->nama,
            'total' => $request->total,
            'tipe' => 3
        ];

        Jurnal::create($data);

        return redirect()->route('biaya')->with('success', 'Biaya created successfully.');
        
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
        $biayas = Akun::where("tipe","=","biaya")->get();
        return view('biaya.edit', compact('jurnal','biayas'));
    
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
            'debet_id' => 'required|exists:akuns,id',
            'total' => 'required|numeric|min:0',
        ]);

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->update($request->all());
        return back()->with('success', 'Biaya updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jurnal = Jurnal::findOrFail($id);
        try {
            $jurnal->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'Biaya deleted successfully.');
    }
}

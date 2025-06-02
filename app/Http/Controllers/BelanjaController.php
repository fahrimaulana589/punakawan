<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\Konsumsi;
use App\Models\Belanja;
use Illuminate\Http\Request;

class BelanjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Belanja!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $belanjas = Belanja::paginate(10);
        return view('belanja.index', compact('belanjas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $konsumsis = Konsumsi::all();

        return view('belanja.create', compact('konsumsis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createManual()
    {
        $akuns = Akun::all();
        return view('belanja.manual', compact('akuns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'konsumsi_id' => 'required|exists:konsumsis,id',
            'total' => 'required|numeric|min:0',
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
        ]);

        $bahanKonsumsi = Konsumsi::findOrFail($request->konsumsi_id);

        $data =[
            'konsumsi_id' => $bahanKonsumsi->kredit->id, // Assuming 2 is the ID for the "Kas" account
            'pegawai_id' => auth()->user()->pegawai_id,
            'tanggal' => $request->tanggal,
            'total' => $request->total,
        ];

        Belanja::create($data);

        return redirect()->route('belanja')->with('success', 'Belanja created successfully.');
        
    }

    public function storeManual(Request $request)
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
            'debet_id' => $request->debet_id, // Assuming 1 is the ID for the "Belanja" account
            'kredit_id' => $request->kredit_id, // Assuming 2 is the ID for the "Kas" account
            'pegawai_id' => auth()->user()->pegawai_id,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'total' => $request->total,
        ];

        Jurnal::create($data);

        return redirect()->route('belanja')->with('success', 'Belanja created successfully.');
        
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
        $belanja = Belanja::findOrFail($id);
        $akuns = Akun::all();
        $konsumsis = Konsumsi::all();
        return view('belanja.edit', compact('belanja','konsumsis','akuns'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'konsumsi_id' => 'required|exists:konsumsis,id',
            'total' => 'required|numeric|min:0',
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
        ]);

        $belanja = Belanja::findOrFail($id);
        $belanja->update($request->all());
        return back()->with('success', 'Belanja updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $belanja = Belanja::findOrFail($id);
        try {
            $belanja->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'Belanja deleted successfully.');
    }
}

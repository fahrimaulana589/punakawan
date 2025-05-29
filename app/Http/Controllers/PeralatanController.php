<?php

namespace App\Http\Controllers;

use App\Models\Peralatan;
use Illuminate\Http\Request;

class PeralatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Peralatan!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $peralatans = Peralatan::paginate(10);
        return view('peralatan.index', compact('peralatans'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('peralatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'tanggal_aktif' => [
                'required',
                'date',
                'before_or_equal:' . date('Y-m-d'),
            ],
            'tanggal_nonaktif' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_aktif',
                'before_or_equal:' . date('Y-m-d'),
            ],
            'harga' => [
                'required',
                'integer',
                'min:1',
            ],
            'umur_ekonomis' => [
                'required',
                'integer',
                'min:1',
            ],
            'nilai_sisa' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);
        
        
        Peralatan::create($request->all());

        return redirect()->route('peralatan')->with('success', 'Peralatan created successfully.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Peralatan $peralatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peralatan $id)
    {
        $peralatan = $id;
        
        return view('peralatan.edit', compact('peralatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peralatan $id)
    {
        $peralatan = $id;

        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'tanggal_aktif' => [
                'required',
                'date',
                'before_or_equal:' . date('Y-m-d'),
            ],
            'tanggal_nonaktif' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_aktif',
                'before_or_equal:' . date('Y-m-d'),
            ],
            'harga' => [
                'required',
                'integer',
                'min:1',
            ],
            'umur_ekonomis' => [
                'required',
                'integer',
                'min:1',
            ],
            'nilai_sisa' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $peralatan->update( $request->all() );

        return back()->with('success', 'Peralatan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peralatan $id)
    {
        $id->delete();
        return back()->with('success', 'Peralatan deleted successfully.');
    }
}

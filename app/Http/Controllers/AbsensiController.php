<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Kehadiran!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $query = Absensi::query();
        $absensis = filter($query);
        return view('absensi.index', compact('absensis'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Pegawai::all();
        return view('absensi.create',compact('karyawans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                // Cek kombinasi unik tanggal dan pegawai_id
                Rule::unique('absensis')->where(function ($query) use ($request) {
                    return $query->where('pegawai_id', $request->pegawai_id);
                }),
            ],
            'pegawai_id' => 'required|numeric|exists:pegawais,id',
            'status' => 'required|string|in:hadir,alpha,izin,terlambat,sakit',
            'alasan' => 'nullable|string',
        ]);

        Absensi::create($request->all());

        return redirect()->route('absensi')->with('success', 'Kehadiran created successfully.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Absensi $absensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $karyawans = Pegawai::all();

        return view('absensi.edit', compact('absensi','karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Absensi $id)
    {
        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                // Cek kombinasi unik tanggal dan pegawai_id
                Rule::unique('absensis')->where(function ($query) use ($request) {
                    return $query->where('pegawai_id', $request->pegawai_id);
                })
                ->ignore($id->id),
            ],
            'pegawai_id' => 'required|numeric|exists:pegawais,id',
            'status' => 'required|string|in:hadir,alpha,izin,terlambat,sakit',
            'alasan' => 'nullable|string',
        ]);

        $id->update($request->all());
        
        return back()->with('success', 'Kehadiran updated successfully.');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absensi $id)
    {
        try {
            $id->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }
        
        return back()->with('success', 'Kehadiran deleted successfully.');
      
    }
}

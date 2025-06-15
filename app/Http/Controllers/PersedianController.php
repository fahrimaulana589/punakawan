<?php

namespace App\Http\Controllers;

use App\Models\Konsumsi;
use App\Models\Persedian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersedianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Persediaan Bahan Baku!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        // Filter Persedian based on tahun and bulan
        $query = Persedian::query();
        $persedians = filter2($query);
        return view('persedian.index', compact('persedians'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 9); // 10 tahun terakhir (dari sekarang ke belakang)

        $bulans = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];


        $bahan_produksis = Konsumsi::all();
        return view('persedian.create', compact('bulans','years','bahan_produksis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => [
                'required',
                'integer',
                'max:' . date('Y'),
            ],
            'bulan' => [
                'required',
                'integer',
                'between:1,12',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tahun == date('Y') && $value > date('n')) {
                    $fail('Bulan tidak boleh lebih dari bulan saat ini di tahun ini.');
                    }
                },    
            ],
            'konsumsi_id' => [
                'required',
                'string',
                'exists:konsumsis,id',
                Rule::unique('konsumsis')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun)
                                ->where('bulan', $request->bulan);
                }),
            ],
            'total' => [
                'required',
                'numeric',
                'min:1',
            ],
        ]);
        
        Persedian::create($request->all());

        return redirect()->route('persedian')->with('success', 'Persediaan Bahan Baku created successfully.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Persedian $persedian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persedian $id)
    {
        $persedian = $id;
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 9); // 10 tahun terakhir (dari sekarang ke belakang)

        $bulans = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $bahan_produksis = Konsumsi::all();
        
        return view('persedian.edit', compact('persedian','bahan_produksis','years','bulans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Persedian $id)
    {
        $persedian = $id;

        $request->validate([
            'tahun' => [
                'required',
                'integer',
                'max:' . date('Y'),
            ],
            'bulan' => [
                'required',
                'integer',
                'between:1,12',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tahun == date('Y') && $value > date('n')) {
                    $fail('Bulan tidak boleh lebih dari bulan saat ini di tahun ini.');
                    }
                },    
            ],
            'konsumsi_id' => [
                'required',
                'string',
                'exists:konsumsis,id',
                Rule::unique('konsumsis')->where(function ($query) use ($request,$persedian) {
                    return $query->where('tahun', $request->tahun)
                                ->where('bulan', $request->bulan)
                                ->where('id', '!=', $persedian->id);
                }),
            ],
            'total' => [
                'required',
                'numeric',
                'min:1',
            ],
        ]);

        $persedian->update( $request->all() );

        return back()->with('success', 'Persediaan Bahan Baku updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persedian $id)
    {
        try {
            $id->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }
        return back()->with('success', 'Persediaan Bahan Baku deleted successfully.');
    }
}

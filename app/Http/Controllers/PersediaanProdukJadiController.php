<?php

namespace App\Http\Controllers;

use App\Models\PersediaanProdukJadi;
use App\Models\Konsumsi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersediaanProdukJadiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Persediaan Produk Jadi!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        // Filter Persediaan Produk Jadi based on tahun and bulan
        $query = PersediaanProdukJadi::query();
        $persedians = filter2(query: $query);
        return view('persedianproduk.index', compact('persedians'));
    
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


        $produks = Produk::all();
        return view('persedianproduk.create', compact('bulans','years','produks'));
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
            'produk_id' => [
                'required',
                'string',
                'exists:produks,id',
                Rule::unique('persediaan_produk_jadis')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun)
                                ->where('bulan', $request->bulan);
                }),
            ],
            'stok' => [
                'required',
                'numeric',
                'min:1',
                'max:100000000'
            ],
        ]);
        
        $data = $request->all();
        $data['stok_sisa'] = -1;
        PersediaanProdukJadi::create($data);

        return redirect()->route('persedianproduk')->with('success', 'Persediaan Produk Jadi created successfully.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(PersediaanProdukJadi $persedian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersediaanProdukJadi $id)
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

        $produks = Produk::all();
        
        return view('persedianproduk.edit', compact('persedian','produks','years','bulans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersediaanProdukJadi $id)
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
            'produk_id' => [
                'required',
                'string',
                'exists:produks,id',
                Rule::unique('persediaan_produk_jadis')->where(function ($query) use ($request, $persedian) {
                    return $query->where('tahun', $request->tahun)
                                 ->where('bulan', $request->bulan)
                                 ->where('id', '!=', $persedian->id);
                }),
            ],
            'stok' => [
                'required',
                'numeric',
                'min:1',
                'max:100000000'
            ],
            'stok_sisa' => [
                'nullable',
                'numeric',
                'min:-1',
                'max:100000000'
            ],
        ]);

        if (is_null($request->stok_sisa)) {
            $request->merge(['stok_sisa' => $persedian->stok_sisa]);
        }
        
        $persedian->update( $request->all() );

        return back()->with('success', 'Persediaan Produk Jadi updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersediaanProdukJadi $id)
    {
        try {
            $id->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }
        return back()->with('success', 'Persediaan Produk Jadi deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\Konsumsi;
use App\Models\Belanja;
use App\Models\Laporan;
use App\Models\Persedian;
use App\Models\PersediaanProdukJadi;
use App\Models\Peralatan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Filter Jurnal based on type
        $query = Jurnal::query();
        $query->whereIn('tipe', [1, 3]);

        $jurnals = filter3($query);
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
            'total' => 'required|numeric|min:0|max:100000000',
        ]);

        $data =[
            'debet_id' => $request->debet_id, // Assuming 1 is the ID for the "Jurnal" account
            'kredit_id' => $request->kredit_id, // Assuming 2 is the ID for the "Kas" account
            'karyawan_id' => auth()->user()->karyawan_id,
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
    public function print()
    {
        // Ambil year dan month dari request, atau default ke hari ini
        $tahun = request()->get('tahun', now()->year);
        $bulan = request()->get('bulan', now()->month);

        $data = [];

        $startDate = tanggal_awal_laporan($tahun,$bulan);
        $endDate = tanggal_akhir_laporan($tahun,$bulan);
        
        $jurnals = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('tipe',[1,3])
            ->get();

        $belanjas = Belanja::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $gaji = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->where('tipe','=',2)
            ->first();

        $previousMonth = $bulan - 1;
        $previousYear = $tahun;

        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear -= 1;
        }

        $persedians = Persedian::where('tahun','=',$previousYear)
            ->where('bulan','=',$previousMonth)
            ->get();

        $persedianProduks = PersediaanProdukJadi::where('tahun','=',$previousYear)
            ->where('bulan','=',$previousMonth)
            ->get();

        $groupedPersedians = $persedians->groupBy(function ($persedian) {
            return $persedian->bahanProduksi->debet_id;
        });

        $peralatans = Peralatan::where(function ($query) use ($startDate, $endDate) {
            $query
                // Kasus 1: tanggal_nonaktif TIDAK NULL
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereNotNull('tanggal_nonaktif')
                    ->whereDate('tanggal_aktif', '<=', $endDate)
                    ->whereDate('tanggal_nonaktif', '>=', $startDate);
                })

                // Kasus 2: tanggal_nonaktif NULL dan tanggal_aktif <= endDate
                ->orWhere(function ($q) use ($endDate) {
                    $q->whereNull('tanggal_nonaktif')
                    ->whereDate('tanggal_aktif', '<=', $endDate);
                });
        })->get();

        foreach($jurnals as $jurnal){
            $data[] = [
                'tanggal' => $jurnal->tanggal,
                'nama' => $jurnal->nama,
                'total' => $jurnal->total,
                'kredit' => $jurnal->kredit->nama
            ];
        }

        foreach( $groupedPersedians as $key => $value ){
            $nama_akun = Akun::find($key)->nama;
            $totalpersedian = $value->sum(function ($item) {
                return $item->total;
            });

            $data[] = [
                'tanggal' => $startDate,
                'nama' => $nama_akun,
                'total' => $totalpersedian,
                'kredit' => 'MODAL'
            ];
        }

        $akunProduk = Akun::find(15)->nama;
        $totalsisaproduk = $persedianProduks->sum(function ($item) {
            return $item->stokSisaProduk * $item->produk->harga;
        });

        if($persedianProduks->count() != 0){
            $data[] = [
                'tanggal' => $startDate,
                'nama' => $akunProduk,
                'total' => $totalsisaproduk,
                'kredit' => 'MODAL'
            ];
        }

        $modal = Jurnal::where('tanggal', $startDate)
                ->where('kredit_id', 14)
                ->where('debet_id', 6)
                ->orderBy('id')
                ->first();
        if($modal){
            $data[] = [
                'tanggal' => $startDate,
                'nama'=> 'AKUMULASI PENYUSUTAN PERALATAN',
                'kredit_total'=> $modal->total,
                'total'=> 0,
                'kredit' => 'AKUMULASI'
            ];
        }

        $totalperalatan = $peralatans->sum(function ($item) {
        return $item->harga;
        });
        $data[] = [
            'tanggal' => $startDate,
            'nama'=> 'PERALATAN',
            'total'=> $totalperalatan,
            'kredit' => 'MODAL' 
        ];

        foreach($belanjas as $belanja){
            $data[] = [
                'tanggal' => $belanja->tanggal,
                'nama'=> $belanja->bahanProduksi->debet->nama." ( ".$belanja->bahanProduksi->nama." ) ",
                'total'=> $belanja->total,
                'kredit' => $belanja->bahanProduksi->kredit->nama 
            ];
        }

        if($gaji){
            $data[] = [
                'tanggal' => $gaji->tanggal,
                'nama'=> $gaji->nama,
                'total'=> $gaji->total,
                'kredit' => $gaji->kredit->nama 
            ];
        }

        usort($data, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        usort($data, function ($a, $b) {
            // Prioritaskan 'MODAL' paling atas, lalu 'AKUMULASI', lalu lainnya
            $priority = ['MODAL' => 2, 'AKUMULASI' => 1];

            $aPriority = $priority[$a['kredit']] ?? 3;
            $bPriority = $priority[$b['kredit']] ?? 3;

            if ($aPriority !== $bPriority) {
            return $aPriority - $bPriority;
            }
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $modalTotal = 0;
        $lastModalIndex = null;

        foreach($data as $index => $item){
            $isModal = $item['kredit'] === 'MODAL';
            $isAkumulasi = $item['kredit'] === 'AKUMULASI';
            if($isModal){
                $modalTotal += $item['total'];
                $lastModalIndex = $index;
                $data[$index]['kredit_total'] = 0;
                $data[$index]['kredit'] = '';
            }else if ($isAkumulasi){
                $modalTotal -= $item['kredit_total'];
                $lastModalIndex = $index;
                $data[$index]['kredit'] = "AKUMULASI";
            }
            else{
                $data[$index]['kredit_total'] = $item['total'];
            }
        }

        // dd($data);

        $data[$lastModalIndex]['kredit_total'] = $modalTotal;
        $data[$lastModalIndex]['kredit'] = 'MODAL';

        $bulan = nama_bulan($bulan);

        $pdf = Pdf::loadView('laporan.jurnal',compact('data','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Jurnal Bulan '.$bulan.'.pdf');
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
            'total' => 'required|numeric|min:0|max:100000000',
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
        try {
            $jurnal->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'Jurnal deleted successfully.');
    }
}

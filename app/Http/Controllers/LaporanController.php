<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Konsumsi;
use App\Models\Laporan;
use App\Models\Persedian;
use App\Models\Jurnal;
use App\Models\Transaksi;
use App\Models\Gaji;
use App\Models\Peralatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Laporan!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $laporans = Laporan::paginate(10);
        return view('laporan.index', compact('laporans'));
    
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

        return view('laporan.create', compact('bulans','years'));
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
                Rule::unique('laporans')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun);
                })
            ],
        ]);
        
        Laporan::create($request->all());

        return redirect()->route('laporan')->with('success', 'Laporan created successfully.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Laporan $id)
    {
        $bulan = nama_bulan($id->bulan);
        $laporan = $id;
        return view('laporan.show',compact('bulan','laporan'));
    }

    /**
     * Display the specified resource.
     */
    public function penjualan(Laporan $id)
    {

        $startDate = tanggal_awal_laporan($id->tahun,$id->bulan);
        $endDate = tanggal_akhir_laporan($id->tahun,$id->bulan);

        $bulan = nama_bulan($id->bulan);

        $transaksi = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $data = [];

        foreach ($transaksi as $item) {
            $tanggal = $item->tanggal;
            $total = $item->total;

            if (isset($data[$tanggal])) {
                $data[$tanggal] += $total;
            } else {
                $data[$tanggal] = $total;
            }
        }

        $pdf = Pdf::loadView('laporan.penjualan',compact('data','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan Penjualan Bulan '.$bulan.'.pdf');
    }

    /**
     * Display the specified resource.
     */
    public function jurnal(Laporan $id)
    {

        $data = [];

        $isFirstLaporan = Laporan::where('tahun', '<', $id->tahun)
            ->orWhere(function ($query) use ($id) {
                $query->where('tahun', $id->tahun)
                      ->where('bulan', '<', $id->bulan);
            })
            ->doesntExist();

        $startDate = tanggal_awal_laporan($id->tahun,$id->bulan);
        $endDate = tanggal_akhir_laporan($id->tahun,$id->bulan);
        
        $bulan = nama_bulan($id->bulan);

        $jurnals = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $gaji = Gaji::whereBetween('tanggal', [$startDate, $endDate])
            ->first();

        $previousMonth = $id->bulan - 1;
        $previousYear = $id->tahun;

        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear -= 1;
        }

        $persedians = Persedian::where('tahun','=',$previousYear)
            ->where('bulan','=',$previousMonth)
            ->get();

        $groupedPersedians = $persedians->groupBy(function ($persedian) {
            return $persedian->bahanProduksi->debet_id;
        });

        $peralatans = Peralatan::where(function ($query) use ($startDate) {
            $query->where(function ($q) use ($startDate) {
                $q->whereNotNull('tanggal_nonaktif')
                  ->whereDate('tanggal_aktif', '<=', $startDate)
                  ->whereDate('tanggal_nonaktif', '>=', $startDate);
            })
            ->orWhere(function ($q) use ($startDate) {
                $q->whereNull('tanggal_nonaktif')
                  ->whereDate('tanggal_aktif', '<=', $startDate);
            });
        })->get();

        // dump($peralatans);
        // dump($persedians);
        // dump($jurnals);
        // dump($gaji);

        if($isFirstLaporan){
            $kas = 1491000;
            $data[] = [
                'tanggal' => $startDate,
                'nama'=> 'KAS',
                'total'=> $kas,
                'kredit' => 'MODAL' 
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

        if($isFirstLaporan){

            $totalperalatan = $peralatans->sum(function ($item) {
                return $item->harga;
            });
            $data[] = [
                'tanggal' => $startDate,
                'nama'=> 'PERALATAN',
                'total'=> $totalperalatan,
                'kredit' => 'MODAL' 
            ];

        }

        foreach($jurnals as $jurnal){
            $data[] = [
                'tanggal' => $jurnal->tanggal,
                'nama'=> $jurnal->nama,
                'total'=> $jurnal->total,
                'kredit' => $jurnal->kredit->nama 
            ];
        }

        $data[] = [
            'tanggal' => $gaji->tanggal,
            'nama'=> $gaji->nama,
            'total'=> $gaji->total,
            'kredit' => $gaji->kredit->nama 
        ];

        $modalTotal = 0;
        $lastModalIndex = null;

        $fixdata = [];

        foreach($data as $index => $item){
            $isModal = $item['kredit'] === 'MODAL';
            if($isModal){
                $modalTotal += $item['total'];
                $lastModalIndex = $index;
                $data[$index]['kredit_total'] = 0;
                $data[$index]['kredit'] = '';
            }else{
                $data[$index]['kredit_total'] = $item['total'];
            }
        }

        $data[$lastModalIndex]['kredit_total'] = $modalTotal;
        $data[$lastModalIndex]['kredit'] = 'MODAL';

        $pdf = Pdf::loadView('laporan.jurnal',compact('data','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Jurnal Bulan '.$bulan.'.pdf');

        // return view('laporan.jurnal',compact('data','bulan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laporan $id)
    {
        $laporan = $id;
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

        return view('laporan.edit', compact('laporan','years','bulans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laporan $id)
    {
        $laporan = $id;

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
            Rule::unique('laporans')->where(function ($query) use ($request) {
                return $query->where('tahun', $request->tahun);
            })->ignore($id),
            ],
        ]);

        $laporan->update( $request->all() );

        return back()->with('success', 'Laporan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $id)
    {
        $id->delete();
        return back()->with('success', 'Laporan deleted successfully.');
    }
}

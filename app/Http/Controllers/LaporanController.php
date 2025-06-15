<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Belanja;
use App\Models\Konsumsi;
use App\Models\Laporan;
use App\Models\Persedian;
use App\Models\Jurnal;
use App\Models\Transaksi;
use App\Models\Gaji;
use App\Models\Peralatan;
use App\Models\Produk;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
                'min:2025',
                'max:' . date('Y'),
            ],
            'bulan' => [
                'required',
                'integer',
                'between:1,12',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = tanggal_awal_laporan($request->tahun, $value);
                    $endDate = tanggal_akhir_laporan($request->tahun, $value);

                    $gaji = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
                        ->where('tipe','=',2)
                        ->first();

                    if (!$gaji) {
                        $fail('Belum ada data gaji pada tahun dan bulan tersebut.');
                    }
                },
                function ($attribute, $value, $fail) use ($request) {
                    $latestLaporan = Laporan::orderByDesc('tahun')
                        ->orderByDesc('bulan')
                        ->first();
        
                    if (!$latestLaporan) {
                        // Laporan pertama harus Januari 2025
                        if (!($request->tahun == 2025 && $value == 1)) {
                            $fail('Laporan pertama harus untuk Januari 2025.');
                        }
                    } else {
                        // Hitung bulan berikutnya yang valid
                        $nextValid = Carbon::createFromDate($latestLaporan->tahun, $latestLaporan->bulan, 1)
                            ->addMonth();
        
                        if (
                            (int)$request->tahun !== (int)$nextValid->year ||
                            (int)$value !== (int)$nextValid->month
                        ) {
                            $fail('Tahun dan bulan harus tepat satu bulan setelah laporan terakhir: ' .
                                $nextValid->format('F Y') . '.');
                        }
                    }
        
                    // Cek jika tahun sekarang tapi bulan melebihi bulan saat ini
                    if ($request->tahun == date('Y') && $value > date('n')) {
                        $fail('Bulan tidak boleh lebih dari bulan saat ini di tahun ini.');
                    }
                },
                Rule::unique('laporans')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun)
                                 ->where('bulan', $request->bulan);
                }),
            ],
        ]);

        $laporan = Laporan::create($request->all());

        $akuns = data_akun($laporan);
        $data = data_saldo($akuns);

        $nextMonth = Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1)->addMonth();
        $month = Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1); 

        $firstDayNextMonth = $nextMonth->copy()->startOfMonth();
        $firtDayMonth = $month->copy()->startOfMonth();
        
        $Bulan = nama_bulan($nextMonth->month);

        $isFirstLaporan = Laporan::where('tahun', '<', $laporan->tahun)
            ->orWhere(function ($query) use ($laporan) {
                $query->where('tahun', $laporan->tahun)
                      ->where('bulan', '<', $laporan->bulan);
            })
            ->doesntExist();

        $data_akun = data_akun($laporan);
        $ajp = total_ajp($data_akun,$laporan);
            
        if($isFirstLaporan){
            Jurnal::updateOrCreate(
                [
                    'tanggal' => $firstDayNextMonth,
                    'debet_id' => 6,
                    'nama' => "Akumulasi Penyusutan Peralatan Bulan " . $Bulan,
                    'kredit_id' => 14,
                    'tipe' => 1,
                ],
                [
                    'pegawai_id' => auth()->user()->pegawai_id,
                    'total' => $ajp["noref"][8]["kredit"],
                ]
            );
        }else{
            $modal = Jurnal::where('tanggal', $firtDayMonth)
                ->where('kredit_id', 14)
                ->where('debet_id', 6)
                ->orderBy('id')
                ->first();

            Jurnal::updateOrCreate(
                [
                    'tanggal' => $firstDayNextMonth,
                    'debet_id' => 6,
                    'nama' => "Akumulasi Penyusutan Peralatan Bulan " . $Bulan,
                    'kredit_id' => 14,
                    'tipe' => 1,
                ],
                [
                    'pegawai_id' => auth()->user()->pegawai_id,
                    'total' => $ajp["ref"][14]["kredit"] + $modal->total,
                ]
            );
        }

        Jurnal::updateOrCreate(
            [
                'tanggal' => $firstDayNextMonth,
                'debet_id' => 1,
                'nama' => "Saldo Awal Bulan " . $Bulan,
                'kredit_id' => 6,
                'tipe' => 1,
            ],
            [
                'pegawai_id' => auth()->user()->pegawai_id,
                'total' => $data[1]['debet'],
            ]
        );
        
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

        $data = data_jurnal($id);

        $bulan = nama_bulan($id->bulan);

        $pdf = Pdf::loadView('laporan.jurnal',compact('data','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Jurnal Bulan '.$bulan.'.pdf');
    }

    public function bukuBesar(Laporan $id){
        $bulan = nama_bulan($id->bulan);
        
        $mergedData = data_akun($id);

        $pdf = Pdf::loadView('laporan.bukubesar',compact('mergedData','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Buku Besar Bulan '.$bulan.'.pdf');

    }

    public function neracaSaldo(Laporan $id){
        $bulan = nama_bulan($id->bulan);
        
        $akuns = data_akun($id);

        $data = data_saldo($akuns);

        $pdf = Pdf::loadView('laporan.saldo',compact('data','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Saldo Bulan '.$bulan.'.pdf');
    }

    public function ajp(Laporan $id){
        $bulan = nama_bulan($id->bulan);
        
        $data_akuns = data_akun($id);

        $data = data_ajp($data_akuns,$id);
        
        $pdf = Pdf::loadView('laporan.ajp',compact('data','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Saldo Bulan '.$bulan.'.pdf');
    }

    public function rekap(){
        $produks = Produk::all();

        $tanggalAwal = request()->get('start_date') ?: date('Y-m-d');
        $tanggalAkhir = request()->get('end_date') ?: date('Y-m-d');

        // Ambil penjualan sesuai rentang waktu
        // Contoh: ambil semua penjualan produk dalam rentang tanggal
        $penjualans = Penjualan::whereHas('transaksi', function ($query) use ($tanggalAwal, $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->where(function($q) {
                    $q->where('status', '')->orWhere('status', 'selesai');
                });
        })->get();

        // Kelompokkan penjualan berdasarkan produk_id
        $terjualGrouped = $penjualans->groupBy('produk_id')->map(function ($item) {
            return $item->sum('jumlah');
        });

        // Tambahkan properti `terjual` ke setiap produk
        $produks->each(function ($produk) use ($terjualGrouped) {
            $produk->terjual = $terjualGrouped[$produk->id] ?? 0;
        });



        return view('laporan.rekap', compact('produks'));
    }

    public function rekap_print(){
        $produks = Produk::all();

        $tanggalAwal = request()->get('start_date') ?: date('Y-m-d');
        $tanggalAkhir = request()->get('end_date') ?: date('Y-m-d');

        // Ambil penjualan sesuai rentang waktu
        // Contoh: ambil semua penjualan produk dalam rentang tanggal
        $penjualans = Penjualan::whereHas('transaksi', function ($query) use ($tanggalAwal, $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->where(function($q) {
                    $q->where('status', '')->orWhere('status', 'selesai');
                });
        })->get();

        // Kelompokkan penjualan berdasarkan produk_id
        $terjualGrouped = $penjualans->groupBy('produk_id')->map(function ($item) {
            return $item->sum('jumlah');
        });

        // Tambahkan properti `terjual` ke setiap produk
        $produks->each(function ($produk) use ($terjualGrouped) {
            $produk->terjual = $terjualGrouped[$produk->id] ?? 0;
        });

        $pdf = Pdf::loadView('laporan.rekap_print', compact('produks'))->setPaper('A4', 'portrait');
        return $pdf->stream('Rekap Penjualan'.'pdf');

        // return view('laporan.rekap_print', compact('produks'));
    }

    public function hpp(Laporan $id) {
        $bulan = nama_bulan($id->bulan);

        $hari = tanggal_akhir_laporan($id->tahun,$id->bulan);
        
        $data_akuns = data_akun($id);
        $data = data_neracasaldo($data_akuns,$id);
        
        $split = [];

        foreach ($data as $key => $item) {
            $split['Neraca Saldo'][$key] = $item['saldo'];
            $split['Penyesuaian'][$key] = $item['penyesuian'];
            $split['Neraca Saldo Disesuikan'][$key] = $item['saldo_penyesuaian'];
            $split['Laba Rugi'][$key] = $item['laba rugi'];
            $split['Neraca'][$key] = $item['neraca'];
        }
    
        $pdf = Pdf::loadView('laporan.hpp',compact('hari','split','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('HPP Bulan '.$bulan.'.pdf');
    }

    public function neracaLajur(Laporan $id){
        $bulan = nama_bulan($id->bulan);
        
        $data_akuns = data_akun($id);
        $data = data_neracasaldo($data_akuns,$id);
        
        $split = [];

        foreach ($data as $key => $item) {
            $split['Neraca Saldo'][] = $item['saldo'];
            $split['Penyesuaian'][] = $item['penyesuian'];
            $split['Neraca Saldo Disesuikan'][] = $item['saldo_penyesuaian'];
            $split['Laba Rugi'][] = $item['laba rugi'];
            $split['Neraca'][] = $item['neraca'];
        }

        $pdf = Pdf::loadView('laporan.lajur',compact('data','split','bulan'))->setPaper('A3', 'landscape');
        return $pdf->stream('Saldo Bulan '.$bulan.'.pdf');

    }

    public function bulan(Laporan $id){
        $bulan = nama_bulan($id->bulan);

        $hari = tanggal_akhir_laporan($id->tahun,$id->bulan);
        
        $data_akuns = data_akun($id);
        $data = data_neracasaldo($data_akuns,$id);
        
        $split = [];

        foreach ($data as $key => $item) {
            $split['Neraca Saldo'][$key] = $item['saldo'];
            $split['Penyesuaian'][$key] = $item['penyesuian'];
            $split['Neraca Saldo Disesuikan'][$key] = $item['saldo_penyesuaian'];
            $split['Laba Rugi'][$key] = $item['laba rugi'];
            $split['Neraca'][$key] = $item['neraca'];
        }

        $excludedKeys = [1, 2, 3, 4, 5, 6, 7, 13, 14,8,15];

        $filtered = array_filter($data_akuns, function($key) use ($excludedKeys) {
            return !in_array($key, $excludedKeys);
        }, ARRAY_FILTER_USE_KEY);

        // Gabungkan semua elemen jadi satu array
        $bebans = array_merge(...array_values($filtered));

        $pdf = Pdf::loadView('laporan.bulan',compact('hari','bebans','split','bulan'))->setPaper('A4', 'portrait');
        return $pdf->stream('Saldo Bulan '.$bulan.'.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laporan $id)
    {
        $laporan = $id;
        
        $this->recalculateAfter($laporan);

       return back()->with('success', 'Laporan updated successfully.');
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
        // Cek apakah laporan ini adalah laporan terakhir (bulan & tahun terbesar)
        $latestLaporan = Laporan::orderByDesc('tahun')->orderByDesc('bulan')->first();

        if ($id->id !== $latestLaporan->id) {
            return back()->with('error', 'Hanya laporan bulan terakhir yang boleh dihapus.');
        }

        try {
            $id->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }
        return back()->with('success', 'Laporan deleted successfully.');
    }

    function recalculateAfter(Laporan $laporanAwal)
{
    // Ambil semua laporan setelah bulan yang diedit (termasuk bulan itu)
    $laporans = Laporan::where(function ($query) use ($laporanAwal) {
        $query->where('tahun', '>', $laporanAwal->tahun)
              ->orWhere(function ($q) use ($laporanAwal) {
                  $q->where('tahun', $laporanAwal->tahun)
                    ->where('bulan', '>=', $laporanAwal->bulan);
              });
    })->orderBy('tahun')->orderBy('bulan')->get();

    foreach ($laporans as $laporan) {
        $akuns = data_akun($laporan);
        $data = data_saldo($akuns);

        $nextMonth = Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1)->addMonth();
        $month = Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1); 

        $firstDayNextMonth = $nextMonth->copy()->startOfMonth();
        $firstDayMonth = $month->copy()->startOfMonth();
        $bulanNama = nama_bulan($nextMonth->month);

        $isFirstLaporan = Laporan::where('tahun', '<', $laporan->tahun)
            ->orWhere(function ($query) use ($laporan) {
                $query->where('tahun', $laporan->tahun)
                      ->where('bulan', '<', $laporan->bulan);
            })
            ->doesntExist();

        $data_akun = data_akun($laporan);
        $ajp = total_ajp($data_akun, $laporan);

        if ($isFirstLaporan) {
            $totalAjp = $ajp["noref"][8]["kredit"] ?? 0;
        } else {
            $modal = Jurnal::where('tanggal', $firstDayMonth)
                ->where('kredit_id', 14)
                ->where('debet_id', 6)
                ->orderBy('id')
                ->first();

            $totalAjp = ($ajp["ref"][14]["kredit"] ?? 0) + ($modal->total ?? 0);
        }

        // Simpan akumulasi penyusutan
        Jurnal::updateOrCreate(
            [
                'tanggal' => $firstDayNextMonth,
                'debet_id' => 6,
                'nama' => "Akumulasi Penyusutan Peralatan Bulan " . $bulanNama,
                'kredit_id' => 14,
                'tipe' => 1,
            ],
            [
                'pegawai_id' => auth()->user()->pegawai_id,
                'total' => $totalAjp,
            ]
        );

        // Simpan saldo awal
        Jurnal::updateOrCreate(
            [
                'tanggal' => $firstDayNextMonth,
                'debet_id' => 1,
                'nama' => "Saldo Awal Bulan " . $bulanNama,
                'kredit_id' => 6,
                'tipe' => 1,
            ],
            [
                'pegawai_id' => auth()->user()->pegawai_id,
                'total' => $data[1]['debet'] ?? 0,
            ]
        );
    }
}
}

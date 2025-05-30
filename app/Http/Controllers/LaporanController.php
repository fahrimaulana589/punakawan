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

<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\GajiKaryawan;
use App\Models\GajiLainya;
use App\Models\Jurnal;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\Response;

class GajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Delete Laporan Gaji!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $gajis = jurnal::where('tipe','=',2)->paginate(10);

        return view('gaji.index', compact('gajis'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tanggal = '';
        return view('gaji.create',compact('tanggal'));
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $exists = jurnal::whereYear('tanggal', $date->year)
                                  ->whereMonth('tanggal', $date->month)
                                  ->where('tipe','=',2)
                                  ->exists();
        
                    if ($exists) {
                        $fail("Data gaji untuk bulan {$date->translatedFormat('F Y')} sudah ada.");
                    }
                },
            ],
        ]);
    
        if ($validator->fails()) {
            return redirect()->route('gaji.create')
                ->withErrors($validator)
                ->withInput();
        }
    
        // Ambil tanggal dari input
        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal)->startOfDay(); // Hapus waktu
        $end_of_month = $tanggal_akhir->copy()->endOfMonth()->startOfDay();

        $awal_bulan_sebelumnya = $tanggal_akhir->copy()->startOfMonth()->subMonth();
        $akhir_bulan_sebelumnya = $tanggal_akhir->copy()->startOfMonth()->subMonth()->endOfMonth();

        $gaji_sebelumnya = Jurnal::where('tipe', 2)
            ->whereBetween('tanggal', [$awal_bulan_sebelumnya, $akhir_bulan_sebelumnya])
            ->first();

        if(!$gaji_sebelumnya){
            $is_akhir_bulan = $tanggal_akhir->isSameDay($end_of_month);
            if ($is_akhir_bulan) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            } else {
                $target_day = $tanggal_akhir->day + 1;
                $bulan_lalu = $tanggal_akhir->copy()->startOfMonth()->subMonth();

                $max_day_last_month = $bulan_lalu->copy()->endOfMonth()->day;

                if ($target_day <= $max_day_last_month) {
                    $tanggal_awal = $bulan_lalu->copy()->day($target_day);
                } else {
                    $tanggal_awal = $bulan_lalu->copy()->endOfMonth();
                }
            }
        }else{
             // Ambil tanggal dari gaji bulan sebelumnya, lalu tambahkan 1 hari
            $tanggal_awal = Carbon::parse($gaji_sebelumnya->tanggal)->addDay()->startOfDay();
        }

        // Format hasil
        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();
 
        $karyawans = Karyawan::all();
        
        // Ambil absensi berdasarkan rentang tanggal
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
    
        // Buat array tanggal dalam rentang
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }

        // Hasil alpa per karyawan
        $rekapAlpa = [];
        $rekapHadir = [];
        $total_gaji = 0;
        
        foreach ($karyawans as $karyawan) {
            $alpaCount = 0;
            $hadirCount = 0;
            $gaji = $karyawan->gaji;

            foreach ($tanggal_range as $tanggal) {
                // Cek apakah karyawan punya absensi di tanggal itu
                $absen = $absensis->firstWhere(fn ($a) => $a->karyawan_id === $karyawan->id && $a->tanggal === $tanggal);
                if (!$absen || ($absen->status != 'hadir' && $absen->status != 'terlambat')) {
                    $alpaCount++;
                }else{
                    $hadirCount++;
                }
            }

            $gaji = $gaji * $hadirCount;

            foreach($karyawan->penggajians as $penggajian){
                if($penggajian->type == 'potongan_bulanan'){
                    $gaji = $gaji - $penggajian->total;
                }else if($penggajian->type == 'potongan_absensi'){
                    $gaji = $gaji - ($penggajian->total * $alpaCount);
                }else if($penggajian->type == 'tunjangan_bulanan'){
                    $gaji = $gaji + $penggajian->total;
                }else if($penggajian->type == 'tunjangan_harian'){
                    $gaji = $gaji + ($penggajian->total * $hadirCount);
                }               
            }
            $total_gaji += $gaji;
            
            $rekapAlpa[$karyawan->id] = $alpaCount;

            $rekapHadir[$karyawan->id] = $hadirCount;
            
        }
        
        return view('gaji.generate', compact('tanggal','tanggal_awal','tanggal_akhir','total_gaji','karyawans','rekapAlpa','rekapHadir'));
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
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $exists = jurnal::whereYear('tanggal', $date->year)
                                  ->whereMonth('tanggal', $date->month)
                                  ->where('tipe','=',2)
                                  ->exists();
        
                    if ($exists) {
                        $fail("Data gaji untuk bulan {$date->translatedFormat('F Y')} sudah ada.");
                    }
                },
            ],
            'total' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000'
            ],
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $nama = $tanggal->translatedFormat('F Y'); // Contoh: "Mei 2025"

        jurnal::create([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Laporan Gaji ".$nama,
            'karyawan_id' => auth()->user()->karyawan_id,
            'tipe' => 2,
            'debet_id' => 8, // Ganti dengan ID akun debet yang sesuai
            'kredit_id' => 1, // Ganti dengan ID akun kredit yang sesuai
        ]);

        return redirect()->route('gaji')->with('success', 'Laporan Gaji created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeGenerate(Request $request)
    {
        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $exists = Jurnal::whereYear('tanggal', $date->year)
                                  ->whereMonth('tanggal', $date->month)
                                  ->where('tipe','=',2)
                                  ->exists();
        
                    if ($exists) {
                        $fail("Data gaji untuk bulan {$date->translatedFormat('F Y')} sudah ada.");
                    }
                },
            ],
            'total' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000'
            ],
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $nama = $tanggal->translatedFormat('F Y'); // Contoh: "Mei 2025"

        $gaji_bulanan = jurnal::create([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Laporan Gaji ".$nama,
            'karyawan_id' => auth()->user()->karyawan_id,
            'tipe' => 2,
            'debet_id' => 8, // Ganti dengan ID akun debet yang sesuai
            'kredit_id' => 1, // Ganti dengan ID akun kredit yang sesuai
        ]);

        // Ambil tanggal dari input
        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal)->startOfDay(); // Hapus waktu
        $end_of_month = $tanggal_akhir->copy()->endOfMonth()->startOfDay();

        $awal_bulan_sebelumnya = $tanggal_akhir->copy()->startOfMonth()->subMonth();
        $akhir_bulan_sebelumnya = $tanggal_akhir->copy()->startOfMonth()->subMonth()->endOfMonth();

        $gaji_sebelumnya = Jurnal::where('tipe', 2)
            ->whereBetween('tanggal', [$awal_bulan_sebelumnya, $akhir_bulan_sebelumnya])
            ->first();

        if(!$gaji_sebelumnya){
            $is_akhir_bulan = $tanggal_akhir->isSameDay($end_of_month);
            if ($is_akhir_bulan) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            } else {
                $target_day = $tanggal_akhir->day + 1;
                $bulan_lalu = $tanggal_akhir->copy()->startOfMonth()->subMonth();

                $max_day_last_month = $bulan_lalu->copy()->endOfMonth()->day;

                if ($target_day <= $max_day_last_month) {
                    $tanggal_awal = $bulan_lalu->copy()->day($target_day);
                } else {
                    $tanggal_awal = $bulan_lalu->copy()->endOfMonth();
                }
            }
        }else{
             // Ambil tanggal dari gaji bulan sebelumnya, lalu tambahkan 1 hari
            $tanggal_awal = Carbon::parse($gaji_sebelumnya->tanggal)->addDay()->startOfDay();
        }

        // Format hasil
        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();
 
        $karyawans = Karyawan::all();
        
        // Ambil absensi berdasarkan rentang tanggal
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
    
        // Buat array tanggal dalam rentang
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }

        $total_gaji = 0;
        
        foreach ($karyawans as $karyawan) {
            $alpaCount = 0;
            $hadirCount = 0;
            $gaji = $karyawan->gaji;

            foreach ($tanggal_range as $tanggal) {
                // Cek apakah karyawan punya absensi di tanggal itu
                $absen = $absensis->firstWhere(fn ($a) => $a->karyawan_id === $karyawan->id && $a->tanggal === $tanggal);
                if (!$absen || ($absen->status != 'hadir' && $absen->status != 'terlambat')) {
                    $alpaCount++;
                }else{
                    $hadirCount++;
                }
            }

            $gaji = $karyawan->gaji * $hadirCount;
            
            foreach($karyawan->penggajians as $penggajian){
                if($penggajian->type == 'potongan_bulanan'){
                    $gaji = $gaji - $penggajian->total;
                }else if($penggajian->type == 'potongan_absensi'){
                    $gaji = $gaji - ($penggajian->total * $alpaCount);
                }else if($penggajian->type == 'tunjangan_bulanan'){
                    $gaji = $gaji + $penggajian->total;
                }else if($penggajian->type == 'tunjangan_harian'){
                    $gaji = $gaji + ($penggajian->total * $hadirCount);
                }               
            }
            
            $total_gaji += $gaji;
            $gaji_karyawan = GajiKaryawan::create([
                'tanggal' => $tanggal,
                'karyawan_id' => $karyawan->id,
                'gaji_id' => $gaji_bulanan->id,
                'total' => $gaji,
                'gaji_pokok'=> $karyawan->gaji
            ]);

            foreach($karyawan->penggajians as $penggajian){
                if($penggajian->type == 'potongan_bulanan'){
                    $lainya = $penggajian->total;
                }else if($penggajian->type == 'potongan_absensi'){
                    $lainya = ($penggajian->total * $alpaCount);
                }else if($penggajian->type == 'tunjangan_bulanan'){
                    $lainya = $penggajian->total;
                }else if($penggajian->type == 'tunjangan_harian'){
                    $lainya = ($penggajian->total * $hadirCount);
                }               
                GajiLainya::create([
                    'gaji_karyawan_id' => $gaji_karyawan->id,
                    'type' => $penggajian->type,
                    'nama' => $penggajian->nama,
                    'total' => $lainya,
                    'lainya_pokok' => $penggajian->total
                ]);
            }
        }

        return redirect()->route('gaji')->with('success', 'Laporan Gaji created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurnal $gaji)
    {

        $tanggal_akhir = Carbon::parse($gaji->tanggal)->startOfDay(); // Hapus waktu
        $end_of_month = $tanggal_akhir->copy()->endOfMonth()->startOfDay();

        $awal_bulan_sebelumnya = $tanggal_akhir->copy()->startOfMonth()->subMonth();
        $akhir_bulan_sebelumnya = $tanggal_akhir->copy()->startOfMonth()->subMonth()->endOfMonth();

        $gaji_sebelumnya = Jurnal::where('tipe', 2)
            ->whereBetween('tanggal', [$awal_bulan_sebelumnya, $akhir_bulan_sebelumnya])
            ->first();


        if(!$gaji_sebelumnya){
            $is_akhir_bulan = $tanggal_akhir->isSameDay($end_of_month);
            if ($is_akhir_bulan) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            } else {
                $target_day = $tanggal_akhir->day + 1;
                $bulan_lalu = $tanggal_akhir->copy()->startOfMonth()->subMonth();

                $max_day_last_month = $bulan_lalu->copy()->endOfMonth()->day;

                if ($target_day <= $max_day_last_month) {
                    $tanggal_awal = $bulan_lalu->copy()->day($target_day);
                } else {
                    $tanggal_awal = $bulan_lalu->copy()->endOfMonth();
                }
            }
        }else{
            // Ambil tanggal dari gaji bulan sebelumnya, lalu tambahkan 1 hari
            $tanggal_awal = Carbon::parse($gaji_sebelumnya->tanggal)->addDay()->startOfDay();
        }

        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();

        $rekapHadir = [];
        $karyawans = Karyawan::all();
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }
        foreach ($karyawans as $karyawan) {
            $hadirCount = 0;
            foreach ($tanggal_range as $tanggal) {
                $absen = $absensis->firstWhere(fn ($a) => $a->karyawan_id === $karyawan->id && $a->tanggal === $tanggal);
                if ($absen && ($absen->status == 'hadir' || $absen->status == 'terlambat')) {
                    $hadirCount++;
                }
            }
            $rekapHadir[$karyawan->id] = $hadirCount;
        }

        return view('gaji.show',compact('gaji','tanggal_awal','tanggal_akhir','rekapHadir'));
    }

    /**
     * Display the specified resource.
     */
    public function slip(Jurnal $gaji)
    {

        $tanggal_akhir = Carbon::parse($gaji->tanggal)->startOfDay(); // Hapus waktu
        $end_of_month = $tanggal_akhir->copy()->endOfMonth()->startOfDay();

        $is_akhir_bulan = $tanggal_akhir->isSameDay($end_of_month);
        if ($is_akhir_bulan) {
            $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
        } else {
            $target_day = $tanggal_akhir->day + 1;
            $bulan_lalu = $tanggal_akhir->copy()->startOfMonth()->subMonth();

            $max_day_last_month = $bulan_lalu->copy()->endOfMonth()->day;

            if ($target_day <= $max_day_last_month) {
                $tanggal_awal = $bulan_lalu->copy()->day($target_day);
            } else {
                $tanggal_awal = $bulan_lalu->copy()->endOfMonth();
            }
        }

        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();

        $rekapHadir = [];
        $karyawans = Karyawan::all();
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }
        foreach ($karyawans as $karyawan) {
            $hadirCount = 0;
            foreach ($tanggal_range as $tanggal) {
                $absen = $absensis->firstWhere(fn ($a) => $a->karyawan_id === $karyawan->id && $a->tanggal === $tanggal);
                if ($absen && ($absen->status == 'hadir' || $absen->status == 'terlambat')) {
                    $hadirCount++;
                }
            }
            $rekapHadir[$karyawan->id] = $hadirCount;
        }

        $slip = [];

        foreach($gaji->karyawans as $gajiKaryawan){
            $slipgaji = [
                'nama' => $gajiKaryawan->karyawan->nama,
                'kode' => $gajiKaryawan->karyawan->kode,
                'jabatan' => $gajiKaryawan->karyawan->jabatan,
                'alamat' => $gajiKaryawan->karyawan->jabatan,
                'no_hp' => $gajiKaryawan->karyawan->no_hp,
                'total' => $gajiKaryawan->totalRupiah,
                'total_numeric' => $gajiKaryawan->total,
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'gaji_pokok' => $gajiKaryawan->gaji_pokok,
                'hari_kerja' => $rekapHadir[$gajiKaryawan->karyawan_id] ?? 0,
                'lainya' => []
            ];
            foreach($gajiKaryawan->gajiLainyas as $lainya){
                $slipgaji['lainya'][] = [
                    'lainya_pokok' => $lainya->lainya_pokok,
                    'nama' => $lainya->nama,
                    'total' => $lainya->totalRupiah,
                    'type' => $lainya->type
                ];
            }

            $html = view('gaji.slip',compact('slipgaji'))->render();

            $bodyHeight = Browsershot::html($html)
                ->setOption('width', '210mm')
                ->evaluate('document.body.scrollHeight');
            // dd($bodyHeight);

            // Generate PDF dan tampilkan langsung
            $pdf = Browsershot::html($html)
                ->setOption('width', '210mm')
                ->setOption('height', $bodyHeight . 'px')
                ->pdf(); // return binary content

            $slip[] = $pdf;
        }

        $merger = new Merger();
        foreach ($slip as $pdf) {
            $merger->addRaw($pdf);
        }

        // Ambil hasil PDF gabungan
        $mergedPdf = $merger->merge();

        // Kirim ke browser
        return Response::make($mergedPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="gabungan.pdf"',
        ]);

        // return view('gaji.slip',compact('gaji','tanggal_awal','tanggal_akhir','rekapHadir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurnal $gaji)
    {
        $karyawans = Karyawan::select('id', 'nama')->get()->toArray();
        $karyawans = json_encode($karyawans);

        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldKaryawanId = old('karyawan_id', []);    // Default karyawan_id kosong
        $oldNominal = old('nominal', []);         // Default nominal kosong

        // Gabungkan karyawan_id dan nominal menjadi array asosiatif
        $old = [];
        foreach ($oldKaryawanId as $idx => $karyawanId) {
            $old[] = [
            'karyawan_id' => $karyawanId,
            'nominal' => $oldNominal[$idx] ?? null,
            ];
        }

        // Jika tidak ada old input, isi dari data gaji karyawan terkait (jika ada relasi)
        if (empty($oldKaryawanId) && empty($oldNominal) && isset($gaji->karyawans)) {
            foreach ($gaji->karyawans as $gajiKaryawan) {
                $old[] = [
                    'karyawan_id' => $gajiKaryawan->karyawan_id,
                    'nominal' => $gajiKaryawan->total,
                ];
            }
        }

        $old = json_encode($old);

        $errors = session('errors');
        $messages = $errors ? $errors->messages() : [];

        $messages = json_encode($messages);

        return view('gaji.edit',compact('old','gaji', 'karyawans','messages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $gaji)
    {

        $request->validate([
            'karyawan_id'   => ['nullable', 'array'],
            'karyawan_id.*' => 'required|integer|exists:karyawans,id',
            'nominal'      => ['nullable', 'array'],
            'nominal.*'    => 'required|numeric|min:1|max:100000000',
        ]);

        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) use ($gaji) {
                    $date = Carbon::parse($value);
                    $exists = jurnal::whereYear('tanggal', $date->year)
                                ->whereMonth('tanggal', $date->month)
                                ->where('id', '!=', $gaji->id) // ❗ Pengecualian data yang sedang diupdate
                                ->where('tipe','=',2)
                                ->exists();

                    if ($exists) {
                        $fail("Data gaji untuk bulan {$date->translatedFormat('F Y')} sudah ada.");
                    }
                },
            ],
            'total' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000'
            ],
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $nama = $tanggal->translatedFormat('F Y');

        $gaji->fill([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Laporan Gaji " . $nama,
        ])->save();

        $gaji->karyawans()->delete(); // Hapus data gaji karyawan lama

        $totalGajiBaru = 0;

        if ($request->has('karyawan_id') && $request->has('nominal')) {
            foreach ($request->karyawan_id as $idx => $karyawanId) {
            $nominal = $request->nominal[$idx];
            GajiKaryawan::create([
                'tanggal' => $request->tanggal,
                'gaji_id' => $gaji->id,
                'karyawan_id' => $karyawanId,
                'total' => $nominal,
            ]);
            $totalGajiBaru += $nominal;
            }

            // Update total gaji di jurnal sesuai jumlah total GajiKaryawan
            $gaji->total = $totalGajiBaru;
            $gaji->save();
        }



        return back()->with('success', 'Laporan Gaji updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $gaji)
    {
        try {
            $gaji->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Data cannot be deleted because it is associated with other records.');
        }

        return back()->with('success', 'Laporan Gaji delated successfully.');
   
    }
}

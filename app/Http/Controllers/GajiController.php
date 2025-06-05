<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\GajiKaryawan;
use App\Models\GajiLainya;
use App\Models\Jurnal;
use App\Models\Pegawai;
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
        $title = 'Delete Gaji!';
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
        $tanggal_akhir = Carbon::parse($tanggal);

        // Cek apakah tanggal akhir adalah hari terakhir di bulan
        $is_akhir_bulan = $tanggal_akhir->isSameDay($tanggal_akhir->copy()->endOfMonth());

        if ($is_akhir_bulan) {
            // Jika hari terakhir bulan ini → ambil tanggal 1 di bulan ini
            $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
        } else {
            // Bukan hari terakhir → coba ambil hari +1 di bulan sebelumnya
            $target_day = $tanggal_akhir->day + 1;
            $bulan_lalu = $tanggal_akhir->copy()->subMonth();

            $tanggal_awal = $bulan_lalu->copy()->day($target_day);

            // Jika tidak valid (keluar dari bulan sebelumnya), fallback ke tanggal 1 bulan ini
            if ($tanggal_awal->month !== $bulan_lalu->month) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            }
        }

        // Format hasil
        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();
 
        $karyawans = Pegawai::all();
        
        // Ambil absensi berdasarkan rentang tanggal
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
    
        // Buat array tanggal dalam rentang
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }

        // Hasil alpa per pegawai
        $rekapAlpa = [];
        $rekapHadir = [];
        $total_gaji = 0;
        
        foreach ($karyawans as $pegawai) {
            $alpaCount = 0;
            $hadirCount = 0;
            $gaji = $pegawai->gaji;

            foreach ($tanggal_range as $tanggal) {
                // Cek apakah pegawai punya absensi di tanggal itu
                $absen = $absensis->firstWhere(fn ($a) => $a->pegawai_id === $pegawai->id && $a->tanggal === $tanggal);
                if (!$absen || ($absen->status != 'hadir' && $absen->status != 'terlambat')) {
                    $alpaCount++;
                }else{
                    $hadirCount++;
                }
            }
            foreach($pegawai->penggajians as $penggajian){
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
            $total_gaji += $gaji * $hadirCount;
            
            $rekapAlpa[$pegawai->id] = $alpaCount;

            $rekapHadir[$pegawai->id] = $hadirCount;
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
            ],
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $nama = $tanggal->translatedFormat('F Y'); // Contoh: "Mei 2025"

        jurnal::create([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Gaji ".$nama,
            'pegawai_id' => auth()->user()->pegawai_id,
            'tipe' => 2,
            'debet_id' => 8, // Ganti dengan ID akun debet yang sesuai
            'kredit_id' => 1, // Ganti dengan ID akun kredit yang sesuai
        ]);

        return redirect()->route('gaji')->with('success', 'Gaji created successfully.');
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
            ],
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $nama = $tanggal->translatedFormat('F Y'); // Contoh: "Mei 2025"

        $gaji_bulanan = jurnal::create([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Gaji ".$nama,
            'pegawai_id' => auth()->user()->pegawai_id,
            'tipe' => 2,
            'debet_id' => 8, // Ganti dengan ID akun debet yang sesuai
            'kredit_id' => 1, // Ganti dengan ID akun kredit yang sesuai
        ]);

        // Ambil tanggal dari input
        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal);

        // Cek apakah tanggal akhir adalah hari terakhir di bulan
        $is_akhir_bulan = $tanggal_akhir->isSameDay($tanggal_akhir->copy()->endOfMonth());

        if ($is_akhir_bulan) {
            // Jika hari terakhir bulan ini → ambil tanggal 1 di bulan ini
            $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
        } else {
            // Bukan hari terakhir → coba ambil hari +1 di bulan sebelumnya
            $target_day = $tanggal_akhir->day + 1;
            $bulan_lalu = $tanggal_akhir->copy()->subMonth();

            $tanggal_awal = $bulan_lalu->copy()->day($target_day);

            // Jika tidak valid (keluar dari bulan sebelumnya), fallback ke tanggal 1 bulan ini
            if ($tanggal_awal->month !== $bulan_lalu->month) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            }
        }

        // Format hasil
        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();
 
        $karyawans = Pegawai::all();
        
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
        
        foreach ($karyawans as $pegawai) {
            $alpaCount = 0;
            $hadirCount = 0;
            $gaji = $pegawai->gaji;

            foreach ($tanggal_range as $tanggal) {
                // Cek apakah pegawai punya absensi di tanggal itu
                $absen = $absensis->firstWhere(fn ($a) => $a->pegawai_id === $pegawai->id && $a->tanggal === $tanggal);
                if (!$absen || ($absen->status != 'hadir' && $absen->status != 'terlambat')) {
                    $alpaCount++;
                }else{
                    $hadirCount++;
                }
            }

            $gaji = $pegawai->gaji * $hadirCount;
            
            foreach($pegawai->penggajians as $penggajian){
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
                'pegawai_id' => $pegawai->id,
                'gaji_id' => $gaji_bulanan->id,
                'total' => $gaji,
                'gaji_pokok'=> $pegawai->gaji
            ]);

            foreach($pegawai->penggajians as $penggajian){
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

        return redirect()->route('gaji')->with('success', 'Gaji created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurnal $gaji)
    {

        $tanggal_akhir = Carbon::parse($gaji->tanggal);
        $is_akhir_bulan = $tanggal_akhir->isSameDay($tanggal_akhir->copy()->endOfMonth());

        if ($is_akhir_bulan) {
            $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
        } else {
            $target_day = $tanggal_akhir->day + 1;
            $bulan_lalu = $tanggal_akhir->copy()->subMonth();
            $tanggal_awal = $bulan_lalu->copy()->day($target_day);
            if ($tanggal_awal->month !== $bulan_lalu->month) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            }
        }

        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();

        $rekapHadir = [];
        $karyawans = Pegawai::all();
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }
        foreach ($karyawans as $pegawai) {
            $hadirCount = 0;
            foreach ($tanggal_range as $tanggal) {
                $absen = $absensis->firstWhere(fn ($a) => $a->pegawai_id === $pegawai->id && $a->tanggal === $tanggal);
                if ($absen && ($absen->status == 'hadir' || $absen->status == 'terlambat')) {
                    $hadirCount++;
                }
            }
            $rekapHadir[$pegawai->id] = $hadirCount;
        }

        return view('gaji.show',compact('gaji','tanggal_awal','tanggal_akhir','rekapHadir'));
    }

    /**
     * Display the specified resource.
     */
    public function slip(Jurnal $gaji)
    {

        $tanggal_akhir = Carbon::parse($gaji->tanggal);
        $is_akhir_bulan = $tanggal_akhir->isSameDay($tanggal_akhir->copy()->endOfMonth());

        if ($is_akhir_bulan) {
            $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
        } else {
            $target_day = $tanggal_akhir->day + 1;
            $bulan_lalu = $tanggal_akhir->copy()->subMonth();
            $tanggal_awal = $bulan_lalu->copy()->day($target_day);
            if ($tanggal_awal->month !== $bulan_lalu->month) {
                $tanggal_awal = $tanggal_akhir->copy()->startOfMonth();
            }
        }

        $tanggal_awal = $tanggal_awal->toDateString();
        $tanggal_akhir = $tanggal_akhir->toDateString();

        $rekapHadir = [];
        $karyawans = Pegawai::all();
        $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
        $tanggal_range = [];
        $periode = Carbon::parse($tanggal_awal)->copy();
        while ($periode->lte($tanggal_akhir)) {
            $tanggal_range[] = $periode->toDateString();
            $periode->addDay();
        }
        foreach ($karyawans as $pegawai) {
            $hadirCount = 0;
            foreach ($tanggal_range as $tanggal) {
                $absen = $absensis->firstWhere(fn ($a) => $a->pegawai_id === $pegawai->id && $a->tanggal === $tanggal);
                if ($absen && ($absen->status == 'hadir' || $absen->status == 'terlambat')) {
                    $hadirCount++;
                }
            }
            $rekapHadir[$pegawai->id] = $hadirCount;
        }

        $slip = [];

        foreach($gaji->karyawans as $gajiKaryawan){
            $slipgaji = [
                'nama' => $gajiKaryawan->karyawan->nama,
                'total' => $gajiKaryawan->totalRupiah,
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'gaji_pokok' => $gajiKaryawan->gaji_pokok,
                'hari_kerja' => $rekapHadir[$gajiKaryawan->pegawai_id] ?? 0,
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
                ->setOption('width', '58mm')
                ->evaluate('document.body.scrollHeight');
            // dd($bodyHeight);

            // Generate PDF dan tampilkan langsung
            $pdf = Browsershot::html($html)
                ->setOption('width', '58mm')
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
        $pegawais = Pegawai::select('id', 'nama')->get()->toArray();
        $pegawais = json_encode($pegawais);

        // Menangkap data old dari session dan mengatur default jika tidak ada
        $oldPegawaiId = old('pegawai_id', []);    // Default pegawai_id kosong
        $oldNominal = old('nominal', []);         // Default nominal kosong

        // Gabungkan pegawai_id dan nominal menjadi array asosiatif
        $old = [];
        foreach ($oldPegawaiId as $idx => $pegawaiId) {
            $old[] = [
            'pegawai_id' => $pegawaiId,
            'nominal' => $oldNominal[$idx] ?? null,
            ];
        }

        // Jika tidak ada old input, isi dari data gaji karyawan terkait (jika ada relasi)
        if (empty($oldPegawaiId) && empty($oldNominal) && isset($gaji->karyawans)) {
            foreach ($gaji->karyawans as $gajiKaryawan) {
                $old[] = [
                    'pegawai_id' => $gajiKaryawan->pegawai_id,
                    'nominal' => $gajiKaryawan->total,
                ];
            }
        }

        $old = json_encode($old);

        $errors = session('errors');
        $messages = $errors ? $errors->messages() : [];

        $messages = json_encode($messages);

        return view('gaji.edit',compact('old','gaji', 'pegawais','messages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $gaji)
    {

        $request->validate([
            'pegawai_id'   => ['nullable', 'array'],
            'pegawai_id.*' => 'required|integer|exists:pegawais,id',
            'nominal'      => ['nullable', 'array'],
            'nominal.*'    => 'required|numeric|min:1',
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
            ],
        ]);

        $tanggal = Carbon::parse($request->tanggal);
        $nama = $tanggal->translatedFormat('F Y');

        $gaji->fill([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Gaji " . $nama,
        ])->save();

        $gaji->karyawans()->delete(); // Hapus data gaji karyawan lama

        $totalGajiBaru = 0;

        if ($request->has('pegawai_id') && $request->has('nominal')) {
            foreach ($request->pegawai_id as $idx => $pegawaiId) {
            $nominal = $request->nominal[$idx];
            GajiKaryawan::create([
                'tanggal' => $request->tanggal,
                'gaji_id' => $gaji->id,
                'pegawai_id' => $pegawaiId,
                'total' => $nominal,
                'gaji_pokok' => Pegawai::find($pegawaiId)->gaji, // Ambil gaji pokok dari pegawai
            ]);
            $totalGajiBaru += $nominal;
            }

            // Update total gaji di jurnal sesuai jumlah total GajiKaryawan
            $gaji->total = $totalGajiBaru;
            $gaji->save();
        }



        return back()->with('success', 'Gaji updated successfully.');
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

        return back()->with('success', 'Gaji delated successfully.');
   
    }
}

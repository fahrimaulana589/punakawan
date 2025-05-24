<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\GajiKaryawan;
use App\Models\GajiLainya;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

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

        $gajis = Gaji::paginate(10);

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
                    $exists = Gaji::whereYear('tanggal', $date->year)
                                  ->whereMonth('tanggal', $date->month)
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
    
        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal);

        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal);

        $target_hari = $tanggal_akhir->day - 1;
        $bulan_lalu = $tanggal_akhir->copy()->subMonth();

        // jika target_hari < 1, pakai hari terakhir bulan sebelumnya
        if ($target_hari < 1) {
            $tanggal_awal = $bulan_lalu->endOfMonth();
        } elseif ($target_hari > $bulan_lalu->daysInMonth) {
            $tanggal_awal = $bulan_lalu->copy()->endOfMonth();
        } else {
            $tanggal_awal = $bulan_lalu->copy()->setDay($target_hari);
        }

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
            $total_gaji += $gaji;
            
            $rekapAlpa[$pegawai->id] = $alpaCount;

            $rekapHadir[$pegawai->id] = $hadirCount;
        }

        return view('gaji.generate', compact('tanggal','total_gaji','karyawans','rekapAlpa','rekapHadir'));
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
                    $exists = Gaji::whereYear('tanggal', $date->year)
                                  ->whereMonth('tanggal', $date->month)
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

        Gaji::create([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Gaji ".$nama,
        ]);

        return redirect()->route('gaji')->with('success', 'Gaji deleted successfully.');
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
                    $exists = Gaji::whereYear('tanggal', $date->year)
                                  ->whereMonth('tanggal', $date->month)
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

        $gaji_bulanan = Gaji::create([
            'tanggal' => $request->tanggal,
            'total' => $request->total,
            'nama' => "Gaji ".$nama,
        ]);

        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal);

        $tanggal = $request->get('tanggal');
        $tanggal_akhir = Carbon::parse($tanggal);

        $target_hari = $tanggal_akhir->day - 1;
        $bulan_lalu = $tanggal_akhir->copy()->subMonth();

        // jika target_hari < 1, pakai hari terakhir bulan sebelumnya
        if ($target_hari < 1) {
            $tanggal_awal = $bulan_lalu->endOfMonth();
        } elseif ($target_hari > $bulan_lalu->daysInMonth) {
            $tanggal_awal = $bulan_lalu->copy()->endOfMonth();
        } else {
            $tanggal_awal = $bulan_lalu->copy()->setDay($target_hari);
        }

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
                'total' => $total_gaji
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
                    'total' => $lainya
                ]);
            }
        }

        return redirect()->route('gaji')->with('success', 'Gaji added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gaji $gaji)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gaji $gaji)
    {
        return view('gaji.edit',compact('gaji'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gaji $gaji)
    {
        $request->validate([
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) use ($gaji) {
                    $date = Carbon::parse($value);
                    $exists = Gaji::whereYear('tanggal', $date->year)
                                ->whereMonth('tanggal', $date->month)
                                ->where('id', '!=', $gaji->id) // ❗ Pengecualian data yang sedang diupdate
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

        return back()->with('success', 'Gaji updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gaji $gaji)
    {
        $gaji->delete();

        return redirect()->route('gaji')->with('success', 'Gaji added successfully.');
   
    }
}

<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\GajiKaryawan;
use App\Models\Jurnal;
use App\Models\GajiLainya;

class DataImport implements WithMultipleSheets, WithEvents
{
    public function sheets(): array
    {
        return [
            'akun' => new AkunImport(),
            'produk' => new ProdukImport(),
            'bahan produksi' => new BahanProduksiImport(),
            'karyawan' => new KaryawanImport(),
            'penggajian karyawan' => new PenggajianKaryawanImport(),
            'user' => new UserImport(),
            'transaksi' => new TransaksiImport(),
            'detail transaksi' => new DetailTransaksiImport(),
            'pembelian' => new PembelianImport(),
            'gaji' => new GajiImport(),
            'detail gaji' => new GajiKaryawanImport(),
            'persedian' => new PersedianImport(),
            'peralatan' => new PeralatanImport(),
            'jurnal' => new JurnalImport(),
            'profile' => new ProfileImport(),
            'absensi' => new AbsensiImport(),
            'biaya' => new BiayaImport(),
            'persedian produk' => new PersedianProdukImport()
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                // Action setelah import selesai
                // Contoh: \Log::info('Import selesai!');
                
                $transaksi = \App\Models\Transaksi::all();
                foreach ($transaksi as $trx) {
                    $totalPenjualan = $trx->penjualan()->sum('total');
                    if ($totalPenjualan > 0) {
                        $trx->total = $totalPenjualan;
                        $trx->save();
                    }
                }

                $gajiList = \App\Models\Jurnal::where('tipe', 2)->get();

                foreach ($gajiList as $gaji) {
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
                    // Format hasil
                    $tanggal_awal = $tanggal_awal->toDateString();
                    $tanggal_akhir = $tanggal_akhir->toDateString();
     
                    // Ambil absensi berdasarkan rentang tanggal
                    $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
                    
                    $adaAbsensi = $absensis->isNotEmpty();
                    
                    $total_gaji = $gaji->total;
                    // Simpan informasi ke dalam model Gaji
                    if($adaAbsensi){
                        
                        $karyawans = Karyawan::all();
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
                            $gaji_karyawan = $karyawan->gaji;

                            foreach ($tanggal_range as $tanggal) {
                                // Cek apakah karyawan punya absensi di tanggal itu
                                $absen = $absensis->firstWhere(fn ($a) => $a->karyawan_id === $karyawan->id && $a->tanggal === $tanggal);
                                if (!$absen || ($absen->status != 'hadir' && $absen->status != 'terlambat')) {
                                    $alpaCount++;
                                }else{
                                    $hadirCount++;
                                }
                            }

                            $gaji_karyawan = $karyawan->gaji * $hadirCount;

                            foreach($karyawan->penggajians as $penggajian){
                                if($penggajian->type == 'potongan_bulanan'){
                                    $gaji_karyawan = $gaji_karyawan - $penggajian->total;
                                }else if($penggajian->type == 'potongan_absensi'){
                                    $gaji_karyawan = $gaji_karyawan - ($penggajian->total * $alpaCount);
                                }else if($penggajian->type == 'tunjangan_bulanan'){
                                    $gaji_karyawan = $gaji_karyawan + $penggajian->total;
                                }else if($penggajian->type == 'tunjangan_harian'){
                                    $gaji_karyawan = $gaji_karyawan + ($penggajian->total * $hadirCount);
                                }               
                            }

                            $total_gaji += $gaji_karyawan;

                            $gaji_karyawan = GajiKaryawan::create([
                                'tanggal' => $tanggal,
                                'karyawan_id' => $karyawan->id,
                                'gaji_id' => $gaji->id,
                                'total' => $gaji_karyawan,
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
                    } else if($gaji->karyawans()->count() > 0){
                        $total_gaji = 0;
                        foreach($gaji->karyawans as $karyawan){
                            $total_gaji += $karyawan->total;
                        }
                    }

                    $gaji->total = $total_gaji;
                    $gaji->save();
                }
            },
        ];
    }
}

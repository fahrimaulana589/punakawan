<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\GajiKaryawan;
use App\Models\GajiLainya;

class DataImport implements WithMultipleSheets
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
                    // Cek apakah ada absensi di bulan dan tahun gaji
                    // Ambil tanggal dari input
                    $tanggal = $gaji->tanggal;
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
                    
                    // Ambil absensi berdasarkan rentang tanggal
                    $absensis = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->get();
                    
                    $adaAbsensi = $absensis->isNotEmpty();
                    $total_gaji = $gaji->total;
                    // Simpan informasi ke dalam model Gaji
                    if($adaAbsensi){
                        
                        $karyawans = Pegawai::all();
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
                            $gaji_karyawan = $pegawai->gaji;

                            foreach ($tanggal_range as $tanggal) {
                                // Cek apakah pegawai punya absensi di tanggal itu
                                $absen = $absensis->firstWhere(fn ($a) => $a->pegawai_id === $pegawai->id && $a->tanggal === $tanggal);
                                if (!$absen || ($absen->status != 'hadir' && $absen->status != 'terlambat')) {
                                    $alpaCount++;
                                }else{
                                    $hadirCount++;
                                }
                            }

                            $gaji_karyawan = $pegawai->gaji * $hadirCount;

                            foreach($pegawai->penggajians as $penggajian){
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
                                'pegawai_id' => $pegawai->id,
                                'gaji_id' => $gaji->id,
                                'total' => $gaji_karyawan,
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

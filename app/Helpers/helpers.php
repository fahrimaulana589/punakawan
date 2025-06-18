<?php

use App\Models\Akun;
use App\Models\Belanja;
use App\Models\Konsumsi;
use App\Models\Laporan;
use App\Models\PersediaanProdukJadi;
use App\Models\Persedian;
use App\Models\Jurnal;
use App\Models\Transaksi;
use App\Models\Gaji;
use App\Models\Peralatan;
use Illuminate\Support\Carbon;

if (!function_exists('format_uang')) {
    function format_uang($nilai)
    {
        return 'Rp. ' . number_format($nilai, 0, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    function format_tanggal($nilai)
    {
        return \Carbon\Carbon::parse($nilai)->translatedFormat('d F Y');
    }
}

if (!function_exists('nama_bulan')) {
    function nama_bulan($nilai)
    {
        $daftarBulan = [
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

        return $daftarBulan[$nilai] ?? 'Tidak Diketahui';
    }
}

if (!function_exists('tanggal_awal_laporan')) {
    function tanggal_awal_laporan($tahun,$bulan)
    {
        return now()->setYear($tahun)->startOfMonth()->setMonth($bulan)->toDateString();
    }
}

if (!function_exists('tanggal_akhir_laporan')) {
    function tanggal_akhir_laporan($tahun,$bulan)
    {
        return now()->setYear($tahun)->startOfMonth()->setMonth($bulan)->endOfMonth()->toDateString();
    }
}

if (!function_exists('filter')) {
    function filter($query)
    {
        $perPage = request()->get('per_page', 10);
        $tanggalAwal = request()->get('start_date');
        $tanggalAkhir = request()->get('end_date');

        $tahun = null;
        $bulan = null;
        $tahunAkhir = null;
        $bulanAkhir = null;

        if ($tanggalAwal) {
            try {
            $carbonAwal = \Carbon\Carbon::parse($tanggalAwal);
            $tahun = $carbonAwal->year;
            $bulan = $carbonAwal->month;
            } catch (\Exception $e) {
            $tahun = null;
            $bulan = null;
            }
        }

        if ($tanggalAkhir) {
            try {
            $carbonAkhir = \Carbon\Carbon::parse($tanggalAkhir);
            $tahunAkhir = $carbonAkhir->year;
            $bulanAkhir = $carbonAkhir->month;
            } catch (\Exception $e) {
            $tahunAkhir = null;
            $bulanAkhir = null;
            }
        }

        if (request()->route() && (request()->route()->getName() === 'persedian' || request()->route()->getName() === 'persedianproduk')) {
            if ($tahun && $bulan && $tahunAkhir && $bulanAkhir) {
                $query->where(function ($q) use ($tahun, $bulan, $tahunAkhir, $bulanAkhir) {
                    $start = sprintf('%04d%02d', $tahun, $bulan);
                    $end = sprintf('%04d%02d', $tahunAkhir, $bulanAkhir);
                    $q->whereRaw("CONCAT(tahun, LPAD(bulan, 2, '0')) >= ?", [$start])
                      ->whereRaw("CONCAT(tahun, LPAD(bulan, 2, '0')) <= ?", [$end]);
                });
            } elseif ($tahun && $bulan) {
                $query->where('tahun', $tahun)->where('bulan', $bulan);
            } elseif ($tahunAkhir && $bulanAkhir) {
                $query->where('tahun', $tahunAkhir)->where('bulan', $bulanAkhir);
            }
            $query->orderByRaw("tahun desc, bulan desc");
        } else{
            if ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($tanggalAwal) {
                $query->where('tanggal', '>=', $tanggalAwal);
            } elseif ($tanggalAkhir) {
                $query->where('tanggal', '<=', $tanggalAkhir);
            }

            $query->orderBy('tanggal', 'desc');
        }
        return $query->paginate($perPage)->withQueryString(); // <- ini penting
    }

}

if (!function_exists('filter2')) {
    function filter2($query)
    {
        $perPage = request()->get('per_page', 10);
        $tahun = request()->get('year');
        $bulan = request()->get('month');

        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        $query->orderByDesc('tahun')->orderByDesc('bulan');

        return $query->paginate($perPage)->withQueryString(); // <- ini penting
    }

}

if (!function_exists('filter3')) {
    function filter3($query)
    {
        $perPage = request()->get('per_page', 10);

        // Ambil year dan month dari request, atau default ke hari ini
        $tahun = request()->get('year', now()->year);
        $bulan = request()->get('month', now()->month);

        // Buat range tanggal awal dan akhir
        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Jika tahun & bulan sekarang, ambil hanya sampai hari ini
        if ($tahun == now()->year && $bulan == now()->month) {
            $tanggalAkhir = now()->endOfDay();
        }

        // Query berdasarkan rentang tanggal
        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderByDesc('tanggal');

        return $query->paginate($perPage)->withQueryString(); // <- ini penting
    }

}

if (!function_exists('data_akun')) {
    function data_akun(Laporan $id):array{
        $data = [];

        $laporan = $id;

        $isFirstLaporan = Laporan::where('tahun', '<', $id->tahun)
            ->orWhere(function ($query) use ($id) {
                $query->where('tahun', $id->tahun)
                      ->where('bulan', '<', $id->bulan);
            })
            ->doesntExist();

        $startDate = tanggal_awal_laporan($id->tahun,$id->bulan);
        $endDate = tanggal_akhir_laporan($id->tahun,$id->bulan);
        
        $transaksi = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $data_transaksi = [];

        foreach ($transaksi as $item) {
            $tanggal = $item->tanggal;
            $total = $item->total;

            if (isset($data[$tanggal])) {
                $data_transaksi[$tanggal]['total'] += $total;
                $data_transaksi[$tanggal]['nama'] = "Penjualan";

                $data_transaksi[$tanggal]['debet'] = $item->debet->id;
                $data_transaksi[$tanggal]['kredit'] = $item->kredit->id;
            } else {
                $data_transaksi[$tanggal]['total'] = $total;
                $data_transaksi[$tanggal]['nama'] = "Penjualan";

                $data_transaksi[$tanggal]['debet'] = $item->debet->id;
                $data_transaksi[$tanggal]['kredit'] = $item->kredit->id;
            }
        }

        foreach ($data_transaksi as $tanggal => $item) {
            $data[] = [
                'tanggal' => $tanggal,
                'nama' => $item['nama'],
                'total' => $item['total'],
                'debet' => $item['debet'],
                'kredit' => $item['kredit'],
            ];
        }

        $jurnals = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('tipe',[1,3])
            ->where('kredit_id', '!=', 14)
            ->get();

        $belanjas = Belanja::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $gaji = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->where('tipe','=',2)
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
        
        $persedianProduks = PersediaanProdukJadi::where('tahun','=',$previousYear)
            ->where('bulan','=',$previousMonth)
            ->get();

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
                'debet' => $jurnal->debet->id,
                'kredit' => $jurnal->kredit->id,
            ];
        }

        foreach( $persedians as $key => $value ){
            $data[] = [
                'tanggal' => $startDate,
                'nama' => "Saldo Awal ".$value->bahanProduksi->nama,
                'total' => $value->total,
                'kredit' => 6,
                'debet' => $value->bahanProduksi->debet_id
            ];
        }

        $akunProduk = Akun::find(15)->nama;
        $totalsisaproduk = $persedianProduks->sum(function ($item) {
            return $item->stokSisaProduk * $item->produk->harga;
        });

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Saldo Awal ".$akunProduk,
            'total' => $totalsisaproduk,
            'kredit' => 6,
            'debet' => 15
        ];

        foreach($peralatans as $key => $value){
            $data[] = [
                'tanggal' => $startDate,
                'nama'=> $value->nama,
                'total'=> $value->harga,
                'debet' => 5,
                'kredit' => 6, 
            ];
        }

        foreach($belanjas as $belanja){
            $nama_belanja = $belanja->bahanProduksi->debet->nama." (".$belanja->bahanProduksi->nama.")";
            $data[] = [
                'tanggal' => $belanja->tanggal,
                'nama'=> $nama_belanja,
                'total'=> $belanja->total,
                'debet' => $belanja->bahanProduksi->debet->id,
                'kredit' => $belanja->bahanProduksi->kredit->id, 
            ];
        }

        $data[] = [
            'tanggal' => $gaji->tanggal,
            'nama'=> $gaji->nama,
            'total'=> $gaji->total,
            'debet' => $gaji->debet_id,
            'kredit' => $gaji->kredit_id, 
        ];

        $groupedByDebet = [];

        foreach ($data as $item) {
            $debetId = $item['debet'];
            if (!isset($groupedByDebet[$debetId])) {
            $groupedByDebet[$debetId] = [];
            }
            $groupedByDebet[$debetId][] = $item;
        }

        $groupedByKredit = [];

        foreach ($data as $item) {
            $kreditId = $item['kredit'];
            if (!isset($groupedByKredit[$kreditId])) {
            $groupedByKredit[$kreditId] = [];
            }
            $groupedByKredit[$kreditId][] = $item;
        }

        $mergedData = [];

        foreach ($groupedByDebet as $debetId => $items) {
            foreach ($items as $item) {
                $mergedData[$debetId][] = array_merge($item, ['status' => 'debet']);
            }
        }

        foreach ($groupedByKredit as $kreditId => $items) {
            foreach ($items as $item) {
                $mergedData[$kreditId][] = array_merge($item, ['status' => 'kredit']);
            }
        }

        foreach ($mergedData as $id => &$items) {
            usort($items, function ($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });
        }     
        
        $total = 0;
        $modal = [];

        foreach($mergedData[6] as $item) {
            $total += $item['total'];
            $modal = [
                'tanggal' => $item['tanggal'],
                'nama' => "Modal",
                'total' => $total,
                'debet' => $item['debet'],
                'kredit' => $item['kredit'],
                'status' => $item['status'],
             ];
        }

        if(!$isFirstLaporan){
            $akumulasi = Jurnal::where('tanggal', $startDate)
                ->where('kredit_id', 14)
                ->where('debet_id', 6)
                ->orderBy('id')
                ->first();

            $mergedData[14][] = [
                'tanggal' => $startDate,
                'nama'=> 'AKUMULASI PENYUSUTAN PERALATAN',
                'total'=> $akumulasi->total,
                'debet'=> 0,
                'kredit' => 14,
                "status" => "kredit"
            ];

            $modal['total'] -= $akumulasi->total;
        }

        $mergedData[6] = [];
        $mergedData[6][] = $modal;
        
        return $mergedData;
    }
}

if (!function_exists('data_jurnal')) {
    function data_jurnal(Laporan $id)
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
        
        $jurnals = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('tipe',[1,3])
            ->get();

        $belanjas = Belanja::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $gaji = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->where('tipe','=',2)
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

        $data[] = [
            'tanggal' => $startDate,
            'nama' => $akunProduk,
            'total' => $totalsisaproduk,
            'kredit' => 'MODAL'
        ];

        if(!$isFirstLaporan){

            $modal = Jurnal::where('tanggal', $startDate)
                ->where('kredit_id', 14)
                ->where('debet_id', 6)
                ->orderBy('id')
                ->first();

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

        $data[] = [
            'tanggal' => $gaji->tanggal,
            'nama'=> $gaji->nama,
            'total'=> $gaji->total,
            'kredit' => $gaji->kredit->nama 
        ];

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

        return $data;
    }
}

if (!function_exists('data_saldo')) {
    function data_saldo($data_akun)
    {
        $data = [];
    
        foreach($data_akun as $key => $items) {
            $akun = Akun::find($key);
            $totaldebet = 0;
            $totalkredit = 0;

            foreach($items as $item) {
                $total = $item['total'];
                if($item['status'] == 'debet'){
                    $totaldebet += $total;
                    $totalkredit -= $total;
                }else if($item['status'] == 'kredit'){
                    $totaldebet -= $total;
                    $totalkredit += $total;
                }  
            }

            $tipe = "";

            if(in_array($key, [1, 5, 6,4,14])){
                // lakukan sesuatu khusus untuk akun dengan id 1, 4, 5, atau 6
                // contoh: set nilai debet dan kredit ke 0
                $tipe = 'asset';
            }else{
                $tipe = 'beban';
            }

            if ($totaldebet < 0) {
                $totaldebet = 0;
            }
            
            if ($totalkredit < 0) {
                $totalkredit = 0;
            }

            $data[$key] = [
                'id' => $akun->id,
                'kode' => $akun->kode,
                'nama' => $akun->nama,
                'debet' =>$totaldebet,
                'kredit' => $totalkredit,
                'tipe' => $tipe
            ];
        }

        return $data;
    }
}

if (!function_exists('total_pembelian')) {
    function total_pembelian(Laporan $laporan)
    {
        $startDate = tanggal_awal_laporan($laporan->tahun,$laporan->bulan);
        $endDate = tanggal_akhir_laporan($laporan->tahun,$laporan->bulan);
        
        $belanjas = Belanja::whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $groupedBelanjas = $belanjas->groupBy(function ($belanja) {
            return $belanja->bahanProduksi->debet_id;
        });

        $belanjas = [];

        foreach( $groupedBelanjas as $key => $value ){
            $nama_akun = Akun::find($key)->nama;
            $totalbelanja = $value->sum(function ($item) {
                return $item->total;
            });
            
            $belanjas[$key]['nama'] = $nama_akun;
            $belanjas[$key]['total'] = $totalbelanja; 

        }

        return $belanjas;
    }
}

if (!function_exists('data_ajp')) {
    function data_ajp($data_akuns,Laporan $laporan)
    {
        $data = [];

        $data_akuns = data_akun($laporan);

        $total_persediaan = total_persediaan($laporan);
        $total_persediaan_awal = total_persediaan_awal($laporan);
        $total_pembelian = total_pembelian($laporan);
        $data_saldo = data_saldo($data_akuns);

        $saldo_bahan_baku = $data_saldo[2]['debet'];

        $bahan_baku_akhir = $total_persediaan[2]['total'];
        $pemakian_bahan_baku = $saldo_bahan_baku - $bahan_baku_akhir;
        $pembelian_bahan_baku = $total_pembelian[2]['total'];
        $bahan_baku_awal = $total_persediaan_awal[2]['total'];
        
        $saldo_bahan_penolong = $data_saldo[3]['debet'];

        $bahan_penolong_akhir = $total_persediaan[3]['total'];
        $pemakaian_bahan_penolong = $saldo_bahan_penolong - $bahan_penolong_akhir;
        $pembelian_bahan_penolong = $total_pembelian[3]['total'];
        $bahan_penolong_awal = $total_persediaan_awal[3]['total'];

        $saldo_perlengkapan = $data_saldo[4]['debet'];

        $perlengkapan_akhir = $total_persediaan[4]['total'];
        $pemakaian_perlengkapan = $saldo_perlengkapan - $perlengkapan_akhir;
        
        $startDate = tanggal_awal_laporan($laporan->tahun,$laporan->bulan);
        $endDate = tanggal_akhir_laporan($laporan->tahun,$laporan->bulan);
        
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

        $totalBebanPenyustan = $peralatans->sum(function($peralatan){
            return $peralatan->bebanPenyusutan;
        });

        $gaji = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->where('tipe','=',2)
            ->first();

        $saldo_akhir_bb = $pemakian_bahan_baku;
        $total_biaya_bb = $gaji->total;
        $total_bop = $totalBebanPenyustan + $pemakaian_bahan_penolong;

        $persedian_awal_produk = $total_persediaan_awal[15]['total'];
        $persedian_akhir_produk = $total_persediaan[15]['total'];

        $total_biaya_produksi = $total_bop + $saldo_akhir_bb + $total_biaya_bb;         
        $harga_pokok_penjualan = $total_biaya_produksi + $persedian_awal_produk - $persedian_akhir_produk;
        
        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Beban Pokok Penjualan",
            'debet' => $harga_pokok_penjualan,
            'kredit' => 0,
            "status" => "debet",
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[15]['nama']." (akhir)",
            'debet' => $persedian_akhir_produk,
            'kredit' => 0,
            "status" => "debet",
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Harga Pokok Produksi",
            'debet' => 0,
            'kredit' => $total_biaya_produksi,
            "status" => "kredit",
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[15]['nama']." (awal)",
            'debet' => 0,
            'kredit' => $persedian_awal_produk,
            "status" => "kredit"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[2]['nama']." (akhir)",
            'debet' => $bahan_baku_akhir,
            'kredit' => 0,
            "status" => "debet",
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pemakaian ".$total_persediaan[2]['nama'],
            'debet' => $pemakian_bahan_baku,
            'kredit' => 0,
            "status" => "debet"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pembelian ".$total_persediaan[2]['nama'],
            'debet' => 0,
            'kredit' => $pembelian_bahan_baku,
            "status" => "kredit"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[2]['nama']." (awal)",
            'debet' => 0,
            'kredit' => $bahan_baku_awal,
            "status" => "kredit"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[3]['nama']." (akhir)",
            'debet' => $bahan_penolong_akhir,
            'kredit' => 0,
            "status" => "debet"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pemakaian ".$total_persediaan[3]['nama'],
            'debet' => $pemakaian_bahan_penolong,
            'kredit' => 0,
            "status" => "debet"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pembelian ".$total_persediaan[3]['nama'],
            'debet' => 0,
            'kredit' => $pembelian_bahan_penolong,
            "status" => "kredit"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[3]['nama']." (awal)",
            'debet' => 0,
            'kredit' => $bahan_penolong_awal,
            "status" => "kredit"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Beban ".$total_persediaan[4]['nama'],
            'debet' => $pemakaian_perlengkapan,
            'kredit' => 0,
            "status" => "debet"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => $total_persediaan[4]['nama'],
            'debet' => 0,
            'kredit' => $pemakaian_perlengkapan,
            "status" => "kredit"
        ];

        foreach($peralatans as $peralatan){
            $beban_penyusutan = ($peralatan->harga - $peralatan->nilai_sisa) / $peralatan->umur_ekonomis;
            $beban_penyusutan = (int) round($beban_penyusutan);
            $data[] = [
                'tanggal' => $startDate,
                'nama' => "Beban Penyusutan Peralatan ".$peralatan->nama,
                'debet' => $beban_penyusutan,
                'kredit' => 0,
                "status" => "debet"
            ];
            $data[] = [
                'tanggal' => $startDate,
                'nama' => "Akumulasi Penyusutan Peralatan ".$peralatan->nama,
                'debet' => 0,
                'kredit' => $beban_penyusutan,
                "status" => "kredit"
            ];  
        }
               
        return $data;
    }
}

if (!function_exists('total_ajp')) {
    function total_ajp($data_akuns,Laporan $laporan)
    {
        $data = [];

        $isFirstLaporan = Laporan::where('tahun', '<', $laporan->tahun)
            ->orWhere(function ($query) use ($laporan) {
                $query->where('tahun', $laporan->tahun)
                      ->where('bulan', '<', $laporan->bulan);
            })
            ->doesntExist();

        $total_persediaan = total_persediaan($laporan);
        $total_persediaan_awal = total_persediaan_awal($laporan);
        $total_pembelian = total_pembelian($laporan);
        $data_saldo = data_saldo($data_akuns);

        $saldo_bahan_baku = $data_saldo[2]['debet'];

        $bahan_baku_akhir = $total_persediaan[2]['total'];
        $pemakian_bahan_baku = $saldo_bahan_baku - $bahan_baku_akhir;
        $pembelian_bahan_baku = $total_pembelian[2]['total'];
        $bahan_baku_awal = $total_persediaan_awal[2]['total'];
        
        $saldo_bahan_penolong = $data_saldo[3]['debet'];

        $bahan_penolong_akhir = $total_persediaan[3]['total'];
        $pemakaian_bahan_penolong = $saldo_bahan_penolong - $bahan_penolong_akhir;
        $pembelian_bahan_penolong = $total_pembelian[3]['total'];
        $bahan_penolong_awal = $total_persediaan_awal[3]['total'];

        $saldo_perlengkapan = $data_saldo[4]['debet'];

        $perlengkapan_akhir = $total_persediaan[4]['total'];
        $pemakaian_perlengkapan = $saldo_perlengkapan - $perlengkapan_akhir;
        
        $startDate = tanggal_awal_laporan($laporan->tahun,$laporan->bulan);
        $endDate = tanggal_akhir_laporan($laporan->tahun,$laporan->bulan);
        
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

        $totalBebanPenyustan = $peralatans->sum(function($peralatan){
            return $peralatan->bebanPenyusutan;
        });

        $gaji = Jurnal::whereBetween('tanggal', [$startDate, $endDate])
            ->where('tipe','=',2)
            ->first();

        $saldo_akhir_bb = $pemakian_bahan_baku;
        $total_biaya_bb = $gaji->total;
        $total_bop = $totalBebanPenyustan + $pemakaian_bahan_penolong;

        $persedian_awal_produk = $total_persediaan_awal[15]['total'];
        $persedian_akhir_produk = $total_persediaan[15]['total'];

        $total_biaya_produksi = $total_bop + $saldo_akhir_bb + $total_biaya_bb;         
        $harga_pokok_penjualan = $total_biaya_produksi + $persedian_awal_produk - $persedian_akhir_produk;

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[2]['nama']." (akhir)",
            'debet' => $bahan_baku_akhir,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "asset",
            "ref" => "0",
            "index" => "ajp_1"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pemakaian ".$total_persediaan[2]['nama'],
            'debet' => $pemakian_bahan_baku,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "beban",
            "ref" => "0",
            "index" => "ajp_2"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pembelian ".$total_persediaan[2]['nama'],
            'debet' => 0,
            'kredit' => $pembelian_bahan_baku,
            "status" => "kredit",
            "tipe" => "beban",
            "ref" => 2,
            "index" => "ajp_3"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[2]['nama']." (awal)",
            'debet' => 0,
            'kredit' => $bahan_baku_awal,
            "status" => "kredit",
            "tipe" => "beban",
            "ref" => "0",
            "index" => "ajp_4"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[3]['nama']." (akhir)",
            'debet' => $bahan_penolong_akhir,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "asset",
            "ref" => "0",
            "index" => "ajp_5"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pemakaian ".$total_persediaan[3]['nama'],
            'debet' => $pemakaian_bahan_penolong,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "beban",
            "ref" => "0",
            "index" => "ajp_6"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Pembelian ".$total_persediaan[3]['nama'],
            'debet' => 0,
            'kredit' => $pembelian_bahan_penolong,
            "status" => "kredit",
            "tipe" => "beban",
            "ref" => 3,
            "index" => "ajp_7"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[3]['nama']." (awal)",
            'debet' => 0,
            'kredit' => $bahan_penolong_awal,
            "status" => "kredit",
            "tipe" => "beban",
            "ref" => "0",
            "index" => "ajp_8"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Beban ".$total_persediaan[4]['nama'],
            'debet' => $pemakaian_perlengkapan,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "beban",
            "ref" => "0",
            "index" => "ajp_9"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => $total_persediaan[4]['nama'],
            'debet' => 0,
            'kredit' => $pemakaian_perlengkapan,
            "status" => "kredit",
            "tipe" => "asset",
            "ref" => 4,
            "index" => "ajp_10"
        ];

        $total_beban_penyusutan = 0;
        foreach($peralatans as $peralatan){
            $beban_penyusutan = ($peralatan->harga - $peralatan->nilai_sisa) / $peralatan->umur_ekonomis;
            $beban_penyusutan = (int) round($beban_penyusutan);
              
            $total_beban_penyusutan += $beban_penyusutan; 
        }

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Beban Penyusutan Peralatan",
            'debet' => $total_beban_penyusutan,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "beban",
            "ref" => "0",
            "index" => "ajp_11"
        ];

        if($isFirstLaporan){
            $data[] = [
                'tanggal' => $startDate,
                'nama' => "Akumulasi Penyusutan Peralatan",
                'debet' => 0,
                'kredit' => $total_beban_penyusutan,
                "status" => "kredit",
                "tipe" => "asset",
                "ref" => 0,
                "index" => "ajp_12"
            ];
        }else{
            $data[] = [
                'tanggal' => $startDate,
                'nama' => "Akumulasi Penyusutan Peralatan",
                'debet' => 0,
                'kredit' => $total_beban_penyusutan,
                "status" => "kredit",
                "tipe" => "asset",
                "ref" => 14,
                "index" => "ajp_12"
            ];
        }

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Beban Pokok Penjualan",
            'debet' => $harga_pokok_penjualan,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "beban",
            "ref" => 0,
            "index" => "ajp_13"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[15]['nama']." (akhir)",
            'debet' => $persedian_akhir_produk,
            'kredit' => 0,
            "status" => "debet",
            "tipe" => "asset",
            "ref" => 0,
            "index" => "ajp_14"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Harga Pokok Produksi",
            'debet' => 0,
            'kredit' => $total_biaya_produksi,
            "status" => "kredit",
            "tipe" => "beban",
            "ref" => 0,
            "index" => "ajp_15"
        ];

        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[15]['nama']." (awal)",
            'debet' => 0,
            'kredit' => $persedian_awal_produk,
            "status" => "kredit",
            "tipe" => "asset",
            "ref" => 15,
            "index" => "ajp_16"
        ];

        $split = [];

        foreach($data as $item){
            if($item['ref'] == 0){
                $split['noref'][] = $item;
            }else{
                $split['ref'][$item['ref']] = $item;
            }
        }
               
        return $split;
    }
}

if (!function_exists('data_neracasaldo')) {
    function data_neracasaldo($data_akuns,Laporan $laporan)
    {
        $data = [];

        $data_saldo = data_saldo($data_akuns);
        $total_ajp = total_ajp($data_akuns,$laporan);
        
        $count = 0;
        foreach($data_saldo as $saldo){
            $saldo_akun = $saldo;
            $penyesuian = isset($total_ajp['ref'][$saldo['id']]) ? $total_ajp['ref'][$saldo['id']] : [
                "kode" => $saldo['kode'],
                "nama" => $saldo["nama"],
                "debet" => 0,
                "kredit" => 0
            ];
            $debet = ($saldo['debet'] + $penyesuian['debet']) - ($penyesuian['kredit'] + $saldo['kredit']);
            $kredit = ($saldo['kredit'] + $penyesuian['kredit']) - ($penyesuian['debet'] + $saldo['debet']);
            if ($debet < 0) $debet = 0;
            if ($kredit < 0) $kredit = 0;
            $saldo_penyesuiana = [
                "kode" => $saldo['kode'],
                "nama" => $saldo["nama"],
                "debet" => $debet,
                "kredit" => $kredit,
            ];
            $laba_rugi = $saldo['tipe'] == "beban" ? $saldo_penyesuiana : [
                "kode" => $saldo['kode'],
                "nama" => $saldo["nama"],
                "debet" => 0,
                "kredit" => 0
            ];
            $neraca = $saldo['tipe'] == "asset" ? $saldo_penyesuiana : [
                "kode" => $saldo['kode'],
                "nama" => $saldo["nama"],
                "debet" => 0,
                "kredit" => 0
            ];
            $data["saldo_".$saldo['id']] = [
                "saldo" => $saldo_akun,
                "penyesuian" => $penyesuian,
                "saldo_penyesuaian" => $saldo_penyesuiana,
                "laba rugi" => $laba_rugi,
                "neraca" => $neraca
            ];
        }

        foreach($total_ajp['noref'] as $ajp){
            $ajp_akun = [
                "kode" => "",
                "nama" => $ajp["nama"],
                "debet" => 0,
                "kredit" => 0
            ];
            $penyesuian = [
                "kode" => "",
                "nama" => $ajp["nama"],
                "debet" => $ajp['debet'],
                "kredit" => $ajp["kredit"],
            ];
            $laba_rugi = $ajp['tipe'] == "beban" ? $penyesuian : [
                "kode" => "",
                "nama" => $ajp["nama"],
                "debet" => 0,
                "kredit" => 0
            ];
            $neraca = $ajp['tipe'] == "asset" ? $penyesuian : [
                "kode" => "",
                "nama" => $ajp["nama"],
                "debet" => 0,
                "kredit" => 0
            ];
            $data[$ajp["index"]] = [
                "saldo" => $ajp_akun,
                "penyesuian" => $penyesuian,
                "saldo_penyesuaian" => $penyesuian,
                "laba rugi" => $laba_rugi,
                "neraca" => $neraca
            ];
        }

        return $data;
    }
}

if (!function_exists('total_persediaan')) {
    function total_persediaan(Laporan $laporan)
    {
        //ambil persedian bulan ini
        $persedians = Persedian::where('tahun', $laporan->tahun)
            ->where('bulan', $laporan->bulan)
            ->get();

        $persedianProduks = PersediaanProdukJadi::where('tahun','=',$laporan->tahun)
            ->where('bulan','=',$laporan->bulan)
            ->get();

        $groupedPersedians = $persedians->groupBy(function ($persedian) {
            return $persedian->bahanProduksi->debet_id;
        });

        $persedians = [];
        foreach( $groupedPersedians as $key => $value ){
            $nama_akun = Akun::find($key)->nama;
            $totalpersedian = $value->sum(function ($item) {
                return $item->total;
            });
            
            $persedians[$key]['nama'] = $nama_akun;
            $persedians[$key]['total'] = $totalpersedian; 

        }

        $akunProduk = Akun::find(15)->nama;
        $totalsisaproduk = $persedianProduks->sum(function ($item) {
            return $item->stokSisaProduk * $item->produk->harga;
        });

        $persedians[15]['nama'] = $akunProduk;
        $persedians[15]['total'] = $totalsisaproduk;

        return $persedians;
    }
}

if (!function_exists('total_persediaan_awal')) {
    function total_persediaan_awal(Laporan $laporan)
    {
        $previousMonth = $laporan->bulan - 1;
        $previousYear = $laporan->tahun;

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

        $persedians = [];
        foreach( $groupedPersedians as $key => $value ){
            $nama_akun = Akun::find($key)->nama;
            $totalpersedian = $value->sum(function ($item) {
                return $item->total;
            });
            
            $persedians[$key]['nama'] = $nama_akun;
            $persedians[$key]['total'] = $totalpersedian; 
        }

        $persedianProduks = PersediaanProdukJadi::where('tahun','=',$previousYear)
            ->where('bulan','=',$previousMonth)
            ->get();

        $akunProduk = Akun::find(15)->nama;
        $totalsisaproduk = $persedianProduks->sum(function ($item) {
            return $item->stokSisaProduk * $item->produk->harga;
        });

        $persedians[15]['nama'] = $akunProduk;
        $persedians[15]['total'] = $totalsisaproduk;

        return $persedians;
    }
}

if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $satuan = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($angka < 12) {
            return $satuan[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            return terbilang($angka / 10) . " puluh " . terbilang($angka % 10);
        } elseif ($angka < 200) {
            return "seratus " . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return terbilang($angka / 100) . " ratus " . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return "seribu " . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang($angka / 1000) . " ribu " . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang($angka / 1000000) . " juta " . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return terbilang($angka / 1000000000) . " miliar " . terbilang($angka % 1000000000);
        } else {
            return "terlalu besar";
        }
    }
}
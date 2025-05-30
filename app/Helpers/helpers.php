<?php

use App\Models\Akun;
use App\Models\Belanja;
use App\Models\Konsumsi;
use App\Models\Laporan;
use App\Models\Persedian;
use App\Models\Jurnal;
use App\Models\Transaksi;
use App\Models\Gaji;
use App\Models\Peralatan;

if (!function_exists('format_uang')) {
    function format_uang($nilai)
    {
        return 'Rp. ' . number_format($nilai, 0, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    function format_tanggal($nilai)
    {
        return \Carbon\Carbon::parse($nilai)->format('d M Y');
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
        return now()->setYear($tahun)->setMonth($bulan)->startOfMonth()->toDateString();
    }
}

if (!function_exists('tanggal_akhir_laporan')) {
    function tanggal_akhir_laporan($tahun,$bulan)
    {
        return now()->setYear($tahun)->setMonth($bulan)->endOfMonth()->toDateString();
    }
}

if (!function_exists('data_akun')) {
    function data_akun(Laporan $id):array{
        $data = [];

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
            ->where('tipe','=',1)
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

        if($isFirstLaporan){
            foreach($jurnals as $jurnal){
                $data[] = [
                    'tanggal' => $jurnal->tanggal,
                    'nama' => $jurnal->nama,
                    'total' => $jurnal->total,
                    'debet' => $jurnal->debet->id,
                    'kredit' => $jurnal->kredit->id,
                ];
            }
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
            ->where('tipe','=',1)
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

        if($isFirstLaporan){
            foreach($jurnals as $jurnal){
                $data[] = [
                    'tanggal' => $jurnal->tanggal,
                    'nama' => $jurnal->nama,
                    'total' => $jurnal->total,
                    'kredit' => $jurnal->kredit->nama
                ];
            }
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
            if ($a['kredit'] === 'MODAL' && $b['kredit'] !== 'MODAL') {
                return -1;
            }
            if ($a['kredit'] !== 'MODAL' && $b['kredit'] === 'MODAL') {
                return 1;
            }
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $modalTotal = 0;
        $lastModalIndex = null;

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

            if ($totaldebet < 0) {
                $totaldebet = 0;
            }
            
            if ($totalkredit < 0) {
                $totalkredit = 0;
            }

            $data[$key] = [
                'kode' => $akun->kode,
                'nama' => $akun->nama,
                'debet' =>$totaldebet,
                'kredit' => $totalkredit
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


        $data[] = [
            'tanggal' => $startDate,
            'nama' => "Persedian ".$total_persediaan[2]['nama']." (akhir)",
            'debet' => $bahan_baku_akhir,
            'kredit' => 0,
            "status" => "debet"
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

if (!function_exists('total_persediaan')) {
    function total_persediaan(Laporan $laporan)
    {
        //ambil persedian bulan ini
        $persedians = Persedian::where('tahun', $laporan->tahun)
            ->where('bulan', $laporan->bulan)
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

        return $persedians;
    }
}
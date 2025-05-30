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
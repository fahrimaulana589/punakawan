<?php

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

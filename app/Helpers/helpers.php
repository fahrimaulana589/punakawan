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
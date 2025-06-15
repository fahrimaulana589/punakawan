<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Imports\DataImport;
use App\Models\Akun;
use App\Models\Pegawai;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $kasir_role = Role::create(['name' => 'Kasir']);
        $bagian_keuangan_role = Role::create(['name' => 'Bagian Keuangan']);
        $bagian_produksi_role = Role::create(['name' => 'Bagian Produksi']);
        $bagian_sdm_role = Role::create(['name' => 'Bagian SDM']);

        $dashboard = Permission::create(['name' => 'dashboard']);

        $transaksi_kasir = Permission::create(['name' => 'transaksi_kasir']);
        $transaksi_create = Permission::create(['name' => 'transaksi_create']);
        $transaksi_read = Permission::create(['name' => 'transaksi_read']);
        $rekap_penjualan = Permission::create(['name' => 'rekap_penjualan']);
        $transaksi_edit = Permission::create(['name' => 'transaksi_edit']);
        $transaksi_delete = Permission::create(['name' => 'transaksi_delete']);

        $produk_create = Permission::create(['name' => 'produk_create']);
        $produk_read = Permission::create(['name' => 'produk_read']);
        $produk_edit = Permission::create(['name' => 'produk_edit']);
        $produk_delete = Permission::create(['name' => 'produk_delete']);

        $akun_create = Permission::create(['name' => 'akun_create']);
        $akun_read = Permission::create(['name' => 'akun_read']);
        $akun_edit = Permission::create(['name' => 'akun_edit']);
        $akun_delete = Permission::create(['name' => 'akun_delete']);

        $bahan_produksi_create = Permission::create(['name' => 'bahan_produksi_create']);
        $bahan_produksi_read = Permission::create(['name' => 'bahan_produksi_read']);
        $bahan_produksi_edit = Permission::create(['name' => 'bahan_produksi_edit']);
        $bahan_produksi_delete = Permission::create(['name' => 'bahan_produksi_delete']);

        $karyawan_create = Permission::create(['name' => 'karyawan_create']);
        $karyawan_read = Permission::create(['name' => 'karyawan_read']);
        $karyawan_edit = Permission::create(['name' => 'karyawan_edit']);
        $karyawan_delete = Permission::create(['name' => 'karyawan_delete']);

        $user_create = Permission::create(['name' => 'user_create']);
        $user_read = Permission::create(['name' => 'user_read']);
        $user_edit = Permission::create(['name' => 'user_edit']);
        $user_delete = Permission::create(['name' => 'user_delete']);

        $belanja_create = Permission::create(['name' => 'belanja_create']);
        $belanja_read = Permission::create(['name' => 'belanja_read']);
        $belanja_edit = Permission::create(['name' => 'belanja_edit']);
        $belanja_delete = Permission::create(['name' => 'belanja_delete']);

        $jurnal_create = Permission::create(['name' => 'jurnal_create']);
        $jurnal_read = Permission::create(['name' => 'jurnal_read']);
        $jurnal_edit = Permission::create(['name' => 'jurnal_edit']);
        $jurnal_delete = Permission::create(['name' => 'jurnal_delete']);

        $biaya_create = Permission::create(['name' => 'biaya_create']);
        $biaya_read = Permission::create(['name' => 'biaya_read']);
        $biaya_edit = Permission::create(['name' => 'biaya_edit']);
        $biaya_delete = Permission::create(['name' => 'biaya_delete']);


        $absensi_create = Permission::create(['name' => 'absensi_create']);
        $absensi_read = Permission::create(['name' => 'absensi_read']);
        $absensi_edit = Permission::create(['name' => 'absensi_edit']);
        $absensi_delete = Permission::create(['name' => 'absensi_delete']);

        $persedian_create = Permission::create(['name' => 'persedian_create']);
        $persedian_read = Permission::create(['name' => 'persedian_read']);
        $persedian_edit = Permission::create(['name' => 'persedian_edit']);
        $persedian_delete = Permission::create(['name' => 'persedian_delete']);

        $persedianproduk_create = Permission::create(['name' => 'persedianproduk_create']);
        $persedianproduk_read = Permission::create(['name' => 'persedianproduk_read']);
        $persedianproduk_edit = Permission::create(['name' => 'persedianproduk_edit']);
        $persedianproduk_delete = Permission::create(['name' => 'persedianproduk_delete']);

        $laporan_create = Permission::create(['name' => 'laporan_create']);
        $laporan_read = Permission::create(['name' => 'laporan_read']);
        $laporan_edit = Permission::create(['name' => 'laporan_edit']);
        $laporan_delete = Permission::create(['name' => 'laporan_delete']);

        $laporan_penjualan = Permission::create(['name' => 'laporan_penjualan']);
        $laporan_jurnal = Permission::create(['name' => 'laporan_jurnal']);
        $buku_besar = Permission::create(['name' => 'buku_besar']);
        $neraca_saldo = Permission::create(['name' => 'neraca_saldo']);
        $ajp = Permission::create(['name' => 'ajp']);
        $neraca_lajur = Permission::create(['name' => 'neraca_lajur']);
        $hpp = Permission::create(['name' => 'hpp']);
        $laporan_bulan = Permission::create(['name' => 'laporan_bulan']);

        $peralatan_create = Permission::create(['name' => 'peralatan_create']);
        $peralatan_read = Permission::create(['name' => 'peralatan_read']);
        $peralatan_edit = Permission::create(['name' => 'peralatan_edit']);
        $peralatan_delete = Permission::create(['name' => 'peralatan_delete']);

        $gaji_create = Permission::create(['name' => 'gaji_create']);
        $gaji_read = Permission::create(['name' => 'gaji_read']);
        $gaji_edit = Permission::create(['name' => 'gaji_edit']);
        $gaji_delete = Permission::create(['name' => 'gaji_delete']);
        
        $profile_permission = Permission::create(['name' => 'profile_manage']);
        
        $kasir_role->givePermissionTo([
            'dashboard','transaksi_kasir','produk_read',
            'transaksi_create','transaksi_edit','transaksi_delete',
        ]);

        $bagian_produksi_role->givePermissionTo([
            'dashboard',
            'belanja_create','belanja_read','belanja_edit','belanja_delete',
            'persedian_create','persedian_read','persedian_edit','persedian_delete',
            'persedianproduk_create','persedianproduk_read','persedianproduk_edit','persedianproduk_delete',
            'produk_create','produk_read','produk_edit','produk_delete',
            'bahan_produksi_create','bahan_produksi_read','bahan_produksi_edit','bahan_produksi_delete',
            'hpp',
        ]);

        $bagian_sdm_role->givePermissionTo([
            'dashboard',
            'karyawan_create','karyawan_read','karyawan_edit','karyawan_delete',
            'absensi_create','absensi_read','absensi_edit','absensi_delete',
            'user_create','user_read','user_edit','user_delete',
            'gaji_create','gaji_read','gaji_edit','gaji_delete',
            'profile_manage'
        ]);

        $bagian_keuangan_role->givePermissionTo([
            'dashboard',
            'transaksi_read','transaksi_create','transaksi_edit','transaksi_delete',
            'laporan_create','laporan_read','laporan_edit','laporan_delete',
            'belanja_read',
            'rekap_penjualan','gaji_read',
            'jurnal_create','jurnal_read','jurnal_edit','jurnal_delete',
            'biaya_create','biaya_read','biaya_edit','biaya_delete',
            'peralatan_create','peralatan_read','peralatan_edit','peralatan_delete',
            'akun_create','akun_read','akun_edit','akun_delete',
            'laporan_penjualan',
            'laporan_jurnal',
            'buku_besar',
            'neraca_saldo',
            'ajp',
            'neraca_lajur',
            'laporan_bulan',
        ]);
        
        $path = storage_path('/data/data.xlsx');
        Excel::import(new DataImport(), $path);

    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Akun;
use App\Models\Pegawai;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Pegawai::factory(10)->create()->each(function ($pegawai, $index) {
            $pegawai->kode = 'PGW' . str_pad($pegawai->id, 4, '0', STR_PAD_LEFT);
            $pegawai->save();
        });

        $kasir_role = Role::create(['name' => 'Kasir']);
        $direktur_keuangan_role = Role::create(['name' => 'Direktur keuangan']);
        $direktur_produksi_role = Role::create(['name' => 'Direktur Produksi']);
        $direktur_sdm_role = Role::create(['name' => 'Direktur SDM']);

        $dashboard = Permission::create(['name' => 'dashboard']);

        $transaksi_kasir = Permission::create(['name' => 'transaksi_kasir']);
        $transaksi_create = Permission::create(['name' => 'transaksi_create']);
        $transaksi_read = Permission::create(['name' => 'transaksi_read']);
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

        $absensi_create = Permission::create(['name' => 'absensi_create']);
        $absensi_read = Permission::create(['name' => 'absensi_read']);
        $absensi_edit = Permission::create(['name' => 'absensi_edit']);
        $absensi_delete = Permission::create(['name' => 'absensi_delete']);

        $gaji_create = Permission::create(['name' => 'gaji_create']);
        $gaji_read = Permission::create(['name' => 'gaji_read']);
        $gaji_edit = Permission::create(['name' => 'gaji_edit']);
        $gaji_delete = Permission::create(['name' => 'gaji_delete']);
        
        
        $kasir_role->givePermissionTo([
            'dashboard','transaksi_kasir','produk_read'
        ]);
        
        //buat kasir
        $kasir_user = \App\Models\User::factory()->create([
            'name' => 'Kasir',
            'email' => 'kasir@example.com',
            'pegawai_id' =>1
        ]);

        $kasir_user->assignRole($kasir_role);

        //buat direktur keuangan
        $direktur_keuangan_user = \App\Models\User::factory()->create([
            'name' => 'Direktur Keuangan',
            'email' => 'direktur_keuangan@example.com', 
            'pegawai_id' =>2
        ]);

        $direktur_keuangan_user->assignRole($direktur_keuangan_role);

        //buat direktur produksi
        $direktur_produksi_user = \App\Models\User::factory()->create([
            'name' => 'Direktur Produksi',
            'email' => 'direktur_produksi@example.com',
            'pegawai_id' => 3
        ]);

        $direktur_produksi_user->assignRole($direktur_produksi_role);

        //buat direktur sdm
        $direktur_sdm_user = \App\Models\User::factory()->create([
            'name' => 'Direktur SDM',
            'email' => 'direktur_sdm@example.com',
            'pegawai_id' => 3
        ]);

        $direktur_sdm_user->assignRole($direktur_sdm_role);

        Produk::factory(10)->create()->each(function ($produk, $index) {
            $produk->kode = 'PRD' . str_pad($produk->id, 4, '0', STR_PAD_LEFT);
            $produk->stok = 10;
            $produk->save();
        });

        Akun::factory(10)->create()->each(function ($produk, $index) {
            $produk->kode = 'AKN' . str_pad($produk->id, 4, '0', STR_PAD_LEFT);
            $produk->save();
        });


    }
}

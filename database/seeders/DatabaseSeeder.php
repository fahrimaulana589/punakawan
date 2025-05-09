<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Akun;
use App\Models\Pegawai;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $owner_role = Role::create(['name' => 'owner']);
        $admin_role = Role::create(['name' => 'admin']);
        $kasir_role = Role::create(['name' => 'kasir']);

        //buat admin
        $admin_user = \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $admin_user->assignRole($admin_role);

        //buat owner
        $owner_user = \App\Models\User::factory()->create([
            'name' => 'owner',
            'email' => 'owner@example.com',
        ]);

        $owner_user->assignRole($owner_role);

        //buat kasir
        $kasir_user = \App\Models\User::factory()->create([
            'name' => 'kasir',
            'email' => 'kasir@example.com',
        ]);

        $kasir_user->assignRole($kasir_role);

        Produk::factory(10)->create()->each(function ($produk, $index) {
            $produk->kode = 'PRD' . str_pad($produk->id, 4, '0', STR_PAD_LEFT);
            $produk->save();
        });

        Akun::factory(10)->create()->each(function ($produk, $index) {
            $produk->kode = 'AKN' . str_pad($produk->id, 4, '0', STR_PAD_LEFT);
            $produk->save();
        });

        Pegawai::factory(10)->create()->each(function ($pegawai, $index) {
            $pegawai->kode = 'PGW' . str_pad($pegawai->id, 4, '0', STR_PAD_LEFT);
            $pegawai->save();
        });
    }
}

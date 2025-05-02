<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission agar tidak error
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Role (Jabatan)
        Role::create(['name' => 'Owner']);  // Pemilik (Akses Penuh)
        Role::create(['name' => 'Admin']);  // Admin (Bisa bantu Owner)
        Role::create(['name' => 'Staff']);  // Kasir/Gudang (Terbatas)
        Role::create(['name' => 'Viewer']); // Akuntan (Hanya Lihat)
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun OWNER (Akses Penuh)
        $owner = User::create([
            'name' => 'Big Boss Owner',
            'email' => 'owner@bizniz.io', // <--- Email Login
            'password' => Hash::make('password'), // <--- Password Login
        ]);
        $owner->assignRole('Owner');

        // 2. Buat Akun STAFF (Kasir) - Opsional, untuk tes
        $staff = User::create([
            'name' => 'Kasir Andalan',
            'email' => 'kasir@bizniz.io',
            'password' => Hash::make('password'),
        ]);
        $staff->assignRole('Staff');

        // 3. Buat Akun VIEWER (Akuntan) - Opsional, untuk tes
        $viewer = User::create([
            'name' => 'Si Akuntan',
            'email' => 'finance@bizniz.io',
            'password' => Hash::make('password'),
        ]);
        $viewer->assignRole('Viewer');
    }
}

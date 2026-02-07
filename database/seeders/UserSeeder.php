<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Safety check: Buat role jika belum ada (agar bisa jalan tanpa RoleSeeder)
        $roles = ['Owner', 'Staff', 'Viewer', 'Admin'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $owner = User::firstOrCreate(
            ['email' => 'owner@bengkel.io'],
            [
                'name' => 'Juragan Bengkel',
                'password' => Hash::make('password'),
            ]
        );
        $owner->assignRole('Owner');

        $staff = User::firstOrCreate(
            ['email' => 'mekanik@bengkel.io'],
            [
                'name' => 'Mekanik Handal',
                'password' => Hash::make('password'),
            ]
        );
        $staff->assignRole('Staff');

        $viewer = User::firstOrCreate(
            ['email' => 'finance@bengkel.io'],
            [
                'name' => 'Admin Keuangan',
                'password' => Hash::make('password'),
            ]
        );
        $viewer->assignRole('Viewer');
    }
}

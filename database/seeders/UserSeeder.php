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
            ['email' => 'owner@jsmodify.cloud'],
            [
                'name' => 'Boss',
                'password' => Hash::make('ownerjsmodify2026'),
            ]
        );
        $owner->assignRole('Owner');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Cached Roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Granular Permissions (Safe if they already exist)
        $permissions = [
            'view dashboard',
            'manage inventory',
            'perform sale',
            'view finance',
            'manage users',
            'configure system'
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // 3. Define Roles & Assign Permissions (Using firstOrCreate to prevent errors)

        // A. STAFF
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->syncPermissions(['manage inventory', 'perform sale']); // syncPermissions is safer than givePermissionTo for re-runs

        // B. VIEWER
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions(['view dashboard', 'view finance']);

        // C. OWNER
        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $owner->syncPermissions(['view dashboard', 'manage inventory', 'perform sale', 'view finance', 'manage users']);

        // D. ADMIN
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        // 4. Create the Initial Super Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@bizniz.io'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign role only if not already assigned
        if (!$user->hasRole('Admin')) {
            $user->assignRole('Admin');
        }
    }
}

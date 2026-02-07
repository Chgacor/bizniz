<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage inventory',
            'perform sale',
            'view finance',
            'manage users',
            'configure system'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Owner' => ['view dashboard', 'manage inventory', 'perform sale', 'view finance', 'manage users', 'configure system'],
            'Admin' => $permissions,
            'Staff' => ['manage inventory', 'perform sale'],
            'Viewer' => ['view dashboard', 'view finance'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}

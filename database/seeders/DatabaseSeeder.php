<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Role & Permission
        $this->call(RoleSeeder::class);

        // 2. Setup User (Owner, Staff, dll)
        $this->call(UserSeeder::class);

        // 3. Isi Data Toko (Produk, Customer, Transaksi, Retur)
        $this->call(DummyDataSeeder::class);
    }
}

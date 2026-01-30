<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Role & User Utama (Sudah ada sebelumnya)
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class); // Pastikan UserSeeder Anda membuat user dengan role!

        // 2. Setup Pengaturan Toko
        $this->call(SettingSeeder::class);

        // 3. Data Dummy (Urutan Penting!)
        $this->call(ProductSeeder::class);      // Produk dulu
        $this->call(CustomerSeeder::class);     // Lalu Customer
        $this->call(TransactionSeeder::class);  // Terakhir Transaksi (karena butuh produk & customer)
    }
}

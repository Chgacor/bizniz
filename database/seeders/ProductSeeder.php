<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Kategori Elektronik
            ['name' => 'Mouse Logitech B100', 'code' => 'ACC-001', 'category' => 'Elektronik', 'buy' => 45000, 'sell' => 65000, 'stock' => 20],
            ['name' => 'Keyboard Mechanical', 'code' => 'ACC-002', 'category' => 'Elektronik', 'buy' => 250000, 'sell' => 350000, 'stock' => 10],
            ['name' => 'Kabel HDMI 2M', 'code' => 'ACC-003', 'category' => 'Elektronik', 'buy' => 20000, 'sell' => 45000, 'stock' => 50],
            ['name' => 'Flashdisk Sandisk 32GB', 'code' => 'ACC-004', 'category' => 'Elektronik', 'buy' => 55000, 'sell' => 80000, 'stock' => 30],
            ['name' => 'Headset Gaming Rexus', 'code' => 'ACC-005', 'category' => 'Elektronik', 'buy' => 120000, 'sell' => 185000, 'stock' => 15],

            // Kategori Makanan/Minuman
            ['name' => 'Kopi Kapal Api (Renteng)', 'code' => 'FNB-001', 'category' => 'Makanan', 'buy' => 11000, 'sell' => 15000, 'stock' => 100],
            ['name' => 'Indomie Goreng (Dus)', 'code' => 'FNB-002', 'category' => 'Makanan', 'buy' => 115000, 'sell' => 125000, 'stock' => 25],
            ['name' => 'Aqua Botol 600ml', 'code' => 'FNB-003', 'category' => 'Minuman', 'buy' => 3000, 'sell' => 5000, 'stock' => 200],
            ['name' => 'Teh Pucuk Harum', 'code' => 'FNB-004', 'category' => 'Minuman', 'buy' => 3500, 'sell' => 6000, 'stock' => 150],
            ['name' => 'Chitato Sapi Panggang', 'code' => 'FNB-005', 'category' => 'Makanan', 'buy' => 9000, 'sell' => 12000, 'stock' => 40],

            // ATK
            ['name' => 'Kertas A4 Sinar Dunia', 'code' => 'ATK-001', 'category' => 'ATK', 'buy' => 45000, 'sell' => 55000, 'stock' => 60],
            ['name' => 'Pulpen Standard (Lusin)', 'code' => 'ATK-002', 'category' => 'ATK', 'buy' => 18000, 'sell' => 25000, 'stock' => 30],

            // JASA BENGKEL (Type: service)
            ['name' => 'Jasa Ganti Oli', 'code' => 'SRV-001', 'category' => 'Service', 'type' => 'service', 'buy' => 0, 'sell' => 15000, 'stock' => 1000],
            ['name' => 'Jasa Pasang Karbu', 'code' => 'SRV-002', 'category' => 'Service', 'type' => 'service', 'buy' => 0, 'sell' => 35000, 'stock' => 1000],
            ['name' => 'Jasa Service Ringan', 'code' => 'SRV-003', 'category' => 'Service', 'type' => 'service', 'buy' => 0, 'sell' => 50000, 'stock' => 1000],
            ['name' => 'Jasa Tambal Ban', 'code' => 'SRV-004', 'category' => 'Service', 'type' => 'service', 'buy' => 0, 'sell' => 15000, 'stock' => 1000],
            ['name' => 'Jasa Full Cleaner', 'code' => 'SRV-005', 'category' => 'Service', 'type' => 'service', 'buy' => 0, 'sell' => 25000, 'stock' => 1000],
        ];

        foreach ($products as $item) {
            Product::create([
                'product_code' => $item['code'],
                'name' => $item['name'],
                'category' => $item['category'],

                // PERBAIKAN DI SINI:
                // Jika ada key 'type', pakai itu. Jika tidak ada, anggap 'goods'.
                'type' => $item['type'] ?? 'goods',

                'buy_price' => $item['buy'],
                'sell_price' => $item['sell'],
                'stock_quantity' => $item['stock'],
            ]);
        }
    }
}

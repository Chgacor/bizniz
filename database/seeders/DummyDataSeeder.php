<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan ada User
        $user = User::first() ?? User::factory()->create();

        // 2. Buat Produk BARANG (Goods)
        $goods = [
            ['name' => 'SSD Samsung 500GB', 'price' => 850000, 'type' => 'goods', 'stock' => 50],
            ['name' => 'RAM Corsair 16GB', 'price' => 1200000, 'type' => 'goods', 'stock' => 50],
            ['name' => 'Mouse Logitech Wireless', 'price' => 150000, 'type' => 'goods', 'stock' => 100],
            ['name' => 'Keyboard Mechanical', 'price' => 450000, 'type' => 'goods', 'stock' => 30],
            ['name' => 'Monitor LG 24 Inch', 'price' => 2100000, 'type' => 'goods', 'stock' => 10],
        ];

        $productIds = [];
        foreach ($goods as $item) {
            $p = Product::firstOrCreate(
                ['name' => $item['name']],
                [
                    'price' => $item['price'],
                    'sell_price' => $item['price'],
                    'buy_price' => $item['price'] * 0.75,
                    'stock_quantity' => $item['stock'],
                    'type' => $item['type'],
                    'category' => 'Hardware',
                    'product_code' => 'BRG-' . rand(1000, 9999),
                ]
            );
            $productIds[] = $p;
        }

        // 3. Buat Produk JASA (Services)
        $services = [
            ['name' => 'Jasa Install Ulang Windows', 'price' => 100000, 'type' => 'service'],
            ['name' => 'Jasa Service Mainboard', 'price' => 350000, 'type' => 'service'],
            ['name' => 'Jasa Pembersihan Laptop', 'price' => 75000, 'type' => 'service'],
            ['name' => 'Jasa Rakit PC Gaming', 'price' => 250000, 'type' => 'service'],
        ];

        $serviceIds = [];
        foreach ($services as $item) {
            $s = Product::firstOrCreate(
                ['name' => $item['name']],
                [
                    'price' => $item['price'],
                    'sell_price' => $item['price'],
                    'buy_price' => 0,
                    'stock_quantity' => 0,
                    'type' => $item['type'],
                    'category' => 'Service',
                    'product_code' => 'SRV-' . rand(1000, 9999),
                ]
            );
            $serviceIds[] = $s;
        }

        // 4. Buat Customer Dummy (Manual)
        $dummyCustomers = [
            ['name' => 'Budi Santoso', 'phone' => '081279720342', 'address' => 'Jl. Merdeka No. 1'],
            ['name' => 'Siti Aminah', 'phone' => '081229844902', 'address' => 'Jl. Sudirman No. 45'],
            ['name' => 'Christian', 'phone' => '08912304921', 'address' => 'Komp. Elite Blok A'],
            ['name' => 'Dewi Persik', 'phone' => '085711223344', 'address' => 'Jl. Dangdut No. 1'],
            ['name' => 'Agus Kuncoro', 'phone' => '081399887766', 'address' => 'Jl. Kenangan Mantan No. 99'],
        ];

        $customers = [];
        foreach ($dummyCustomers as $c) {
            $cust = Customer::firstOrCreate(
                ['phone' => $c['phone']],
                ['name' => $c['name'], 'address' => $c['address'], 'email' => strtolower(str_replace(' ', '', $c['name'])) . '@example.com']
            );
            $customers[] = $cust;
        }
        $customers = collect($customers);

        // 5. GENERATE TRANSAKSI
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::create(2026, 2, 1);

        echo "Sedang membuat transaksi palsu... Mohon tunggu...\n";

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $dailyTrxCount = rand(3, 8);

            for ($i = 0; $i < $dailyTrxCount; $i++) {
                $cust = $customers->random();

                $trx = Transaction::create([
                    'invoice_code' => 'INV-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                    'user_id' => $user->id,
                    'customer_id' => $cust->id,
                    'total_amount' => 0,
                    'cash_received' => 0,
                    'change_amount' => 0,
                    'status' => 'completed',
                    'created_at' => $date->copy()->addHours(rand(8, 20)),
                    'updated_at' => $date->copy()->addHours(rand(8, 20)),
                ]);

                $totalBelanja = 0;

                // Beli Barang
                $itemsCount = rand(1, 3);
                for ($j = 0; $j < $itemsCount; $j++) {
                    $prod = $productIds[array_rand($productIds)];
                    $qty = rand(1, 2);

                    TransactionItem::create([
                        'transaction_id' => $trx->id,
                        'product_id' => $prod->id,
                        'name' => $prod->name,
                        'quantity' => $qty,

                        // FIX: Hapus 'price', hanya pakai 'price_at_sale'
                        'price_at_sale' => $prod->price,

                        'created_at' => $trx->created_at,
                        'updated_at' => $trx->created_at,
                    ]);
                    $totalBelanja += $prod->price * $qty;
                }

                // Beli Jasa (50% Chance)
                if (rand(0, 1) == 1) {
                    $serv = $serviceIds[array_rand($serviceIds)];
                    TransactionItem::create([
                        'transaction_id' => $trx->id,
                        'product_id' => $serv->id,
                        'name' => $serv->name,
                        'quantity' => 1,

                        // FIX: Hapus 'price', hanya pakai 'price_at_sale'
                        'price_at_sale' => $serv->price,

                        'created_at' => $trx->created_at,
                        'updated_at' => $trx->created_at,
                    ]);
                    $totalBelanja += $serv->price;
                }

                $trx->update(['total_amount' => $totalBelanja, 'cash_received' => $totalBelanja]);
            }
        }
        echo "SELESAI! Data dummy berhasil dibuat.\n";
    }
}

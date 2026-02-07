<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use App\Models\SalesReturn;
use App\Models\ReturnItem;
use App\Models\User;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Data Master (Produk & Pelanggan)
        $owner = User::role('Owner')->first() ?? User::first();
        $staff = User::role('Staff')->first() ?? User::first();

        $categories = ['Oli', 'Ban', 'Sparepart', 'Aksesoris', 'Jasa Servis'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        $products = [
            ['code' => 'OLI-001', 'name' => 'Oli MPX2 Matic 0.8L', 'cat' => 'Oli', 'type' => 'goods', 'buy' => 45000, 'sell' => 60000, 'stock' => 100],
            ['code' => 'OLI-002', 'name' => 'Oli Shell Advance AX7', 'cat' => 'Oli', 'type' => 'goods', 'buy' => 55000, 'sell' => 75000, 'stock' => 80],
            ['code' => 'BAN-001', 'name' => 'Ban FDR Genzi 80/90-14', 'cat' => 'Ban', 'type' => 'goods', 'buy' => 180000, 'sell' => 230000, 'stock' => 20],
            ['code' => 'BAN-002', 'name' => 'Ban IRC Tubeless 90/90-14', 'cat' => 'Ban', 'type' => 'goods', 'buy' => 210000, 'sell' => 270000, 'stock' => 15],
            ['code' => 'PRT-001', 'name' => 'Kampas Rem Depan Beat', 'cat' => 'Sparepart', 'type' => 'goods', 'buy' => 35000, 'sell' => 55000, 'stock' => 50],
            ['code' => 'PRT-002', 'name' => 'Vanbelt Kit Vario 125', 'cat' => 'Sparepart', 'type' => 'goods', 'buy' => 135000, 'sell' => 185000, 'stock' => 25],
            ['code' => 'ACC-001', 'name' => 'Lampu LED RTD 6 Sisi', 'cat' => 'Aksesoris', 'type' => 'goods', 'buy' => 65000, 'sell' => 110000, 'stock' => 30],
            ['code' => 'SRV-001', 'name' => 'Jasa Ganti Oli', 'cat' => 'Jasa Servis', 'type' => 'service', 'buy' => 0, 'sell' => 10000, 'stock' => 0],
            ['code' => 'SRV-002', 'name' => 'Jasa Servis Ringan', 'cat' => 'Jasa Servis', 'type' => 'service', 'buy' => 0, 'sell' => 45000, 'stock' => 0],
            ['code' => 'SRV-003', 'name' => 'Jasa Pasang Ban', 'cat' => 'Jasa Servis', 'type' => 'service', 'buy' => 0, 'sell' => 20000, 'stock' => 0],
        ];

        foreach ($products as $p) {
            $prod = Product::firstOrCreate(
                ['product_code' => $p['code']],
                [
                    'name' => $p['name'],
                    'category' => $p['cat'],
                    'type' => $p['type'],
                    'buy_price' => $p['buy'],
                    'sell_price' => $p['sell'],
                    'stock_quantity' => $p['stock'],
                ]
            );

            if ($p['type'] === 'goods' && $p['stock'] > 0) {
                StockMovement::create([
                    'product_id' => $prod->id,
                    'user_id' => $owner->id,
                    'type' => 'in',
                    'quantity' => $p['stock'],
                    'description' => 'Stok Awal'
                ]);
            }
        }

        $customers = [
            ['name' => 'Bengkel Lancar Jaya', 'phone' => '081288997766'],
            ['name' => 'Komunitas NMAX', 'phone' => '085711223344'],
            ['name' => 'Budi RX King', 'phone' => '081344556677'],
            ['name' => 'Siti Vario', 'phone' => '089655443322'],
            ['name' => 'Doni Aerox', 'phone' => '087899001122'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(
                ['name' => $c['name']],
                ['phone' => $c['phone'], 'email' => strtolower(str_replace(' ', '', $c['name'])) . '@gmail.com', 'address' => 'Jakarta']
            );
        }

        // 2. Generate Transaksi & Retur
        $allProducts = Product::all();
        $allCustomers = Customer::all();

        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subDays(rand(1, 45))->setTime(rand(9, 17), rand(0, 59));
            $cust = $allCustomers->random();
            $itemsToBuy = $allProducts->random(rand(2, 5));

            $totalAmount = 0;
            $itemsData = [];

            foreach ($itemsToBuy as $prod) {
                $qty = ($prod->type === 'goods') ? rand(1, 3) : 1;
                $price = $prod->sell_price;
                $totalAmount += ($qty * $price);
                $itemsData[] = ['model' => $prod, 'qty' => $qty, 'price' => $price];
            }

            $trx = Transaction::create([
                'invoice_code' => 'INV-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                'user_id' => $staff->id,
                'customer_id' => $cust->id,
                'total_amount' => $totalAmount,
                'cash_received' => $totalAmount + rand(0, 50000),
                'change_amount' => 0,
                'status' => 'completed',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $trx->update(['change_amount' => $trx->cash_received - $totalAmount]);

            foreach ($itemsData as $data) {
                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'product_id' => $data['model']->id,
                    'name' => $data['model']->name,
                    'quantity' => $data['qty'],
                    'price_at_sale' => $data['price'],
                    'created_at' => $date,
                    'updated_at' => $date
                ]);

                if ($data['model']->type === 'goods') {
                    $data['model']->decrement('stock_quantity', $data['qty']);
                    StockMovement::create([
                        'product_id' => $data['model']->id,
                        'user_id' => $staff->id,
                        'type' => 'out',
                        'quantity' => $data['qty'],
                        'description' => 'Penjualan ' . $trx->invoice_code,
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);
                }
            }

            // Skenario Retur (Setiap 5 transaksi ada 1 retur)
            if ($i % 5 == 0) {
                $itemRetur = $trx->items->where('product.type', 'goods')->first();
                if ($itemRetur) {
                    $returnDate = Carbon::parse($trx->created_at)->addDays(rand(1, 3));
                    $qtyRetur = 1;
                    $refund = $itemRetur->price_at_sale * $qtyRetur;

                    $salesReturn = SalesReturn::create([
                        'return_code' => 'RET-' . $returnDate->format('Ymd') . '-' . rand(100, 999),
                        'transaction_id' => $trx->id,
                        'user_id' => $staff->id,
                        'reason' => 'Barang Cacat',
                        'total_refund' => $refund,
                        'created_at' => $returnDate,
                        'updated_at' => $returnDate
                    ]);

                    ReturnItem::create([
                        'return_id' => $salesReturn->id,
                        'product_id' => $itemRetur->product_id,
                        'quantity' => $qtyRetur,
                        'condition' => 'good',
                        'refund_amount' => $refund,
                        'created_at' => $returnDate,
                        'updated_at' => $returnDate
                    ]);

                    $prodRetur = Product::find($itemRetur->product_id);
                    $prodRetur->increment('stock_quantity', $qtyRetur);

                    StockMovement::create([
                        'product_id' => $prodRetur->id,
                        'user_id' => $staff->id,
                        'type' => 'in',
                        'quantity' => $qtyRetur,
                        'description' => 'Retur ' . $salesReturn->return_code,
                        'created_at' => $returnDate,
                        'updated_at' => $returnDate
                    ]);
                }
            }
        }
    }
}

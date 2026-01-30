<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada user, produk, dan customer
        $user = User::first();
        $products = Product::all();
        $customers = Customer::all();

        if ($products->isEmpty()) return;

        // Buat 50 Transaksi Acak
        for ($i = 0; $i < 50; $i++) {

            // Tanggal Acak (Antara hari ini sampai 30 hari lalu)
            $date = Carbon::now()->subDays(rand(0, 30))->setTime(rand(8, 20), rand(0, 59));

            // Pilih Customer Acak (atau null/Guest)
            $customer = (rand(0, 1) == 1) ? $customers->random() : null;

            // Buat Invoice Code
            $invoice = 'INV-' . $date->format('dmY') . '-' . rand(1000, 9999);

            // Pilih 1-5 Produk Acak untuk dibeli
            $itemsToBuy = $products->random(rand(1, 5));
            $totalAmount = 0;
            $itemsData = [];

            foreach ($itemsToBuy as $product) {
                $qty = rand(1, 3);
                $price = $product->sell_price;
                $subtotal = $qty * $price;

                $totalAmount += $subtotal;
                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price_at_sale' => $price,
                ];
            }

            // Simpan Transaksi
            $transaction = Transaction::create([
                'invoice_code' => $invoice,
                'user_id' => $user->id ?? 1,
                'customer_id' => $customer ? $customer->id : null,
                'total_amount' => $totalAmount,
                'cash_received' => $totalAmount + rand(0, 50000),
                'change_amount' => 0,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $transaction->update([
                'change_amount' => $transaction->cash_received - $totalAmount
            ]);

            // Simpan Item Transaksi
            foreach ($itemsData as $data) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'],
                    'price_at_sale' => $data['price_at_sale'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}

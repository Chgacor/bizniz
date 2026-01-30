<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;

class PosController extends Controller
{
    // 1. TAMPILAN HALAMAN POS
    public function index()
    {
        return view('pos.index');
    }

    // 2. SEARCH API (UNTUK JAVASCRIPT)
    public function search(Request $request)
    {
        $query = $request->get('query');

        $products = Product::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('product_code', 'LIKE', "%{$query}%")
            ->where('stock_quantity', '>', 0) // Opsional: Sembunyikan stok 0 (kecuali jasa)
            ->orWhere('type', 'service')      // Jasa selalu muncul
            ->orderBy('name', 'asc')
            ->limit(50)
            ->get();

        return response()->json($products);
    }

    // 3. PROSES CHECKOUT (INTI PERBAIKAN)
    public function checkout(Request $request)
    {
        // Validasi Input
        $request->validate([
            'cart' => 'required|array',
            'cash_received' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id', // Validasi Pelanggan
        ]);

        // Hitung Total
        $totalAmount = 0;
        foreach ($request->cart as $item) {
            $totalAmount += $item['price'] * $item['qty'];
        }

        // Simpan Transaksi Utama (Header)
        $transaction = Transaction::create([
            'invoice_code' => 'INV-' . date('dmY') . '-' . rand(1000, 9999),
            'user_id' => auth()->id(), // Kasir yang login
            'customer_id' => $request->customer_id, // <--- PENTING: ID PELANGGAN DISIMPAN
            'total_amount' => $totalAmount,
            'cash_received' => $request->cash_received,
            'change_amount' => $request->cash_received - $totalAmount,
            'status' => 'completed',
        ]);

        // Simpan Detail Item
        // Simpan Detail Item
        foreach ($request->cart as $item) {

            $isCustom = !is_numeric($item['id']);
            $productId = $isCustom ? null : $item['id'];

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productId,
                'quantity' => $item['qty'],
                'price_at_sale' => $item['price'],
            ]);

            // Kurangi stok hanya untuk barang
            if (!$isCustom && ($item['type'] ?? null) === 'goods') {
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('stock_quantity', $item['qty']);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $item['qty'],
                        'description' => 'Penjualan POS: ' . $transaction->invoice_code,
                    ]);
                }
            }
        }


        return response()->json([
            'status' => 'success',
            'invoice' => $transaction->invoice_code
        ]);
    }

    // 4. CETAK STRUK
    public function receipt($invoice_code)
    {
        // Ambil data transaksi beserta item, kasir, dan pelanggan
        $transaction = Transaction::with(['items', 'user', 'customer'])
            ->where('invoice_code', $invoice_code)
            ->firstOrFail();

        return view('pos.receipt', compact('transaction'));
    }
}

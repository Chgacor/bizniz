<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'cart' => 'required|array|min:1',
            'cash_received' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        // Gunakan DB Transaction agar jika satu gagal, semua batal (Aman untuk Stok)
        try {
            return DB::transaction(function () use ($request) {

                // 1. Hitung Total Secara Backend (Jangan percaya total dari frontend)
                $totalAmount = 0;
                foreach ($request->cart as $item) {
                    // Pastikan harga ada, jika tidak default 0
                    $price = isset($item['price']) ? $item['price'] : 0;
                    $qty = isset($item['qty']) ? $item['qty'] : 1;
                    $totalAmount += $price * $qty;
                }

                // 2. Buat Header Transaksi
                $transaction = Transaction::create([
                    'invoice_code'  => 'INV-' . date('Ymd') . '-' . strtoupper(uniqid()),
                    'user_id'       => auth()->id(),
                    'customer_id'   => $request->customer_id,
                    'total_amount'  => $totalAmount,
                    'cash_received' => $request->cash_received,
                    'change_amount' => $request->cash_received - $totalAmount,
                    'status'        => 'completed'
                ]);

                // 3. Proses Setiap Item
                foreach ($request->cart as $cartItem) {
                    $isCustom = isset($cartItem['is_custom']) && $cartItem['is_custom'];
                    $productId = $isCustom ? null : $cartItem['id'];
                    $price = $cartItem['price'];
                    $qty = $cartItem['qty'];

                    // Simpan Detail Item
                    // PENTING: Kita isi 'price' DAN 'price_at_sale' untuk kompatibilitas database
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $productId,
                        'name'           => $cartItem['name'],
                        'quantity'       => $qty,
                        'price'          => $price,       // Kolom standar
                        'price_at_sale'  => $price        // Kolom snapshot harga (Wajib ada nilainya)
                    ]);

                    // 4. Kurangi Stok (Hanya jika Barang Inventaris)
                    if (!$isCustom && $productId) {
                        $product = Product::find($productId);
                        if ($product && $product->type === 'goods') {
                            // Cek stok cukup atau tidak (Opsional, tapi disarankan)
                            if($product->stock_quantity < $qty) {
                                throw new \Exception("Stok {$product->name} tidak cukup!");
                            }
                            $product->decrement('stock_quantity', $qty);
                        }
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'invoice' => $transaction->invoice_code
                ]);
            });

        } catch (\Exception $e) {
            // Jika error, kembalikan pesan ke frontend
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4. CETAK STRUK
    public function receipt($invoice_code)
    {
        // 1. Ambil Data Transaksi
        $transaction = Transaction::where('invoice_code', $invoice_code)
            ->with(['items.product', 'user', 'customer'])
            ->firstOrFail();

        // 2. AMBIL PENGATURAN DARI DATABASE (Supaya Header/Footer berubah)
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('pos.receipt', compact('transaction', 'settings'));
    }
}

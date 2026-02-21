<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Promotion;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        // Tampilkan produk yang ada stoknya ATAU jasa (stok 0 gapapa)
        $products = Product::where('stock_quantity', '>', 0)
            ->orWhere('type', 'service')
            ->orderBy('name')
            ->get();

        $customers = Customer::orderBy('name')->get();

        // Ambil promo aktif hari ini
        $promotions = Promotion::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        return view('pos.index', compact('products', 'customers', 'promotions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'paid_amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,transfer,qris,debit',
        ]);

        DB::beginTransaction();
        try {
            // 1. KUNCI WAKTU KE WIB (PENTING AGAR TIDAK MASUK JAM 12 SIANG SAAT MALAM)
            $timestamp = Carbon::now('Asia/Jakarta');

            $cart = $request->cart;
            $subtotal = 0;

            // Hitung Subtotal & Validasi Stok
            foreach ($cart as $item) {
                if (isset($item['is_custom']) && $item['is_custom']) {
                    $subtotal += $item['price'] * $item['qty'];
                } else {
                    $product = Product::find($item['id']);
                    if (!$product) throw new \Exception("Produk tidak ditemukan dalam database.");

                    // Cek Stok (Hanya untuk Barang)
                    if ($product->type === 'goods' && $product->stock_quantity < $item['qty']) {
                        throw new \Exception("Stok {$product->name} tidak cukup! Sisa: {$product->stock_quantity}");
                    }
                    $subtotal += $product->sell_price * $item['qty'];
                }
            }

            // Hitung Diskon
            $discountAmount = 0;
            $promotionId = null;
            if ($request->manual_discount > 0) {
                $discountAmount = $request->manual_discount;
            } elseif ($request->promotion_id) {
                $promo = Promotion::find($request->promotion_id);
                if ($promo) {
                    $promotionId = $promo->id;
                    $discountAmount = ($promo->discount_type === 'fixed')
                        ? $promo->value
                        : $subtotal * ($promo->value / 100);
                }
            }

            $grandTotal = max($subtotal - $discountAmount, 0);

            // 2. SIMPAN TRANSAKSI (STATUS WAJIB 'COMPLETED')
            $transaction = Transaction::create([
                'invoice_code' => 'INV-' . $timestamp->format('Ymd') . '-' . Str::upper(Str::random(4)),
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'promotion_id' => $promotionId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => 0,
                'total_amount' => $grandTotal,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->paid_amount - $grandTotal,
                'payment_method' => $request->payment_method,
                'status' => 'completed', // <--- KUNCI AGAR MASUK LAPORAN
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            // 3. SIMPAN ITEM & POTONG STOK
            foreach ($cart as $item) {
                $isCustom = isset($item['is_custom']) && $item['is_custom'];
                $product = !$isCustom ? Product::find($item['id']) : null;
                $price = $isCustom ? $item['price'] : $product->sell_price;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product ? $product->id : null,
                    'name' => $isCustom ? $item['name'] : $product->name,
                    'quantity' => $item['qty'],
                    'price_at_sale' => $price,
                    'subtotal' => $price * $item['qty'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                // Logika Potong Stok (Hanya Barang)
                if ($product && $product->type === 'goods') {
                    $product->decrement('stock_quantity', $item['qty']);

                    // Catat di History Gudang
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $item['qty'],
                        'description' => 'Penjualan POS: ' . $transaction->invoice_code,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'invoice_code' => $transaction->invoice_code,
                'change' => $transaction->change_amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function receipt($invoice_code)
    {
        // Cari transaksi beserta item-nya
        $transaction = \App\Models\Transaction::with(['items.product', 'user'])
            ->where('invoice_code', $invoice_code)
            ->firstOrFail();

        return view('pos.receipt', compact('transaction'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Promotion;
use App\Models\StockMovement;
// use App\Models\Setting; // Uncomment jika Model Setting sudah dibuat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock_quantity', '>', 0)
            ->orWhere('type', 'service')
            ->orderBy('name')
            ->get();

        $customers = Customer::orderBy('name')->get();

        // Ambil promo aktif
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
            $cart = $request->cart;
            $subtotal = 0;

            foreach ($cart as $item) {
                if (isset($item['is_custom']) && $item['is_custom']) {
                    $subtotal += $item['price'] * $item['qty'];
                } else {
                    $product = Product::find($item['id']);
                    if (!$product) throw new \Exception("Produk tidak ditemukan.");

                    if ($product->type === 'goods' && $product->stock_quantity < $item['qty']) {
                        throw new \Exception("Stok {$product->name} kurang! Sisa: {$product->stock_quantity}");
                    }
                    $subtotal += $product->sell_price * $item['qty'];
                }
            }

            $discountAmount = 0;
            $promotionId = null;

            if ($request->manual_discount > 0) {
                $discountAmount = min($request->manual_discount, $subtotal);
            } elseif ($request->promotion_id) {
                $promo = Promotion::find($request->promotion_id);
                if ($promo && $promo->isValid()) {
                    $promotionId = $promo->id;
                    $discountAmount = ($promo->discount_type === 'fixed')
                        ? $promo->value
                        : $subtotal * ($promo->value / 100);
                }
            }

            $grandTotal = $subtotal - $discountAmount;

            // Validasi Bayar (Khusus Cash)
            if ($request->payment_method === 'cash' && $request->paid_amount < $grandTotal) {
                throw new \Exception("Uang pembayaran kurang!");
            }

            $transaction = Transaction::create([
                'invoice_code' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
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
            ]);

            foreach ($cart as $item) {
                $isCustom = isset($item['is_custom']) && $item['is_custom'];

                if ($isCustom) {
                    $productId = null;
                    $itemName = $item['name'];
                    $price = $item['price'];
                } else {
                    $product = Product::find($item['id']);
                    $productId = $product->id;
                    $itemName = $product->name;
                    $price = $product->sell_price;
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'name' => $itemName,
                    'quantity' => $item['qty'],
                    'price_at_sale' => $price,
                    'subtotal' => $price * $item['qty'],
                ]);

                if (!$isCustom && $productId) {
                    $prod = Product::find($productId);
                    if ($prod && $prod->type === 'goods') {
                        $prod->decrement('stock_quantity', $item['qty']);
                        StockMovement::create([
                            'product_id' => $prod->id,
                            'user_id' => auth()->id(),
                            'type' => 'out',
                            'quantity' => $item['qty'],
                            'description' => 'POS: ' . $transaction->invoice_code,
                        ]);
                    }
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
        $transaction = Transaction::where('invoice_code', $invoice_code)
            ->with(['items.product', 'user', 'customer'])
            ->firstOrFail();

        $settings = class_exists(\App\Models\Setting::class)
            ? \App\Models\Setting::pluck('value', 'key')->toArray()
            : [];

        return view('pos.receipt', compact('transaction', 'settings'));
    }
}

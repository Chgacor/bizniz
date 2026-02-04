<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function index()
    {
        $invoices = PurchaseInvoice::with(['user', 'items'])->latest()->paginate(10);
        return view('purchase.index', compact('invoices'));
    }

    public function create()
    {
        return view('purchase.create');
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('type', 'goods')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('product_code', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'supplier_name' => 'required|string',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buy_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $invoice = PurchaseInvoice::create([
                'invoice_number' => $request->invoice_number,
                'supplier_name' => $request->supplier_name,
                'invoice_date' => $request->invoice_date,
                'total_amount' => 0,
                'user_id' => auth()->id(),
                'notes' => $request->notes
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['buy_price'];
                $totalAmount += $subtotal;

                PurchaseItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'buy_price' => $item['buy_price'],
                    'subtotal' => $subtotal
                ]);

                $product = Product::find($item['product_id']);
                $product->increment('stock_quantity', $item['quantity']);
                $product->update(['buy_price' => $item['buy_price']]);

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'description' => 'Pembelian Invoice: ' . $invoice->invoice_number,
                ]);
            }

            $invoice->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Stok berhasil ditambahkan!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

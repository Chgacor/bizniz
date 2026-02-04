<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\SalesReturn;
use App\Models\ReturnItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = SalesReturn::with(['transaction', 'user'])->latest()->paginate(10);
        return view('return.index', compact('returns'));
    }

    public function create()
    {
        return view('return.create');
    }

    public function searchTransaction(Request $request)
    {
        $code = $request->query('code');

        $transaction = Transaction::with(['items.product', 'customer'])
            ->where('invoice_code', $code)
            ->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan!']);
        }

        $historyReturns = SalesReturn::with('items')
            ->where('transaction_id', $transaction->id)
            ->get();

        $items = $transaction->items->map(function ($item) use ($historyReturns) {
            $returnedQty = $historyReturns->flatMap->items
                ->where('product_id', $item->product_id)
                ->sum('quantity');

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->name,
                'sold_qty' => $item->quantity,
                'returned_qty' => $returnedQty,
                'available_qty' => $item->quantity - $returnedQty,
                'price' => $item->price_at_sale
            ];
        });

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction,
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'required|in:good,bad',
        ]);

        DB::beginTransaction();
        try {
            $return = SalesReturn::create([
                'return_code' => 'RET-' . date('YmdHis'),
                'transaction_id' => $request->transaction_id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'total_refund' => 0
            ]);

            $totalRefund = 0;

            foreach ($request->items as $item) {
                if ($item['quantity'] > 0) {

                    ReturnItem::create([
                        'return_id' => $return->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'condition' => $item['condition']
                    ]);

                    $product = Product::find($item['product_id']);
                    $refundAmount = $item['price'] * $item['quantity'];
                    $totalRefund += $refundAmount;

                    if ($item['condition'] === 'good' && $product->type === 'goods') {
                        $product->increment('stock_quantity', $item['quantity']);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'type' => 'in',
                            'quantity' => $item['quantity'],
                            'description' => 'Retur Penjualan: ' . $return->return_code,
                        ]);
                    }
                }
            }

            $return->update(['total_refund' => $totalRefund]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Retur berhasil diproses.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $revenue = Transaction::whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        $cogs = TransactionItem::whereHas('transaction', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })
            ->with('product')
            ->get()
            ->sum(function($item) {
                $buyPrice = $item->product ? $item->product->buy_price : 0;
                return $buyPrice * $item->quantity;
            });

        $grossProfit = $revenue - $cogs;

        return view('finance.index', compact('revenue', 'cogs', 'grossProfit'));
    }

    public function getHistory() { return response()->json([]); }
    public function saveCalculation(Request $request) { return response()->json(['status'=>'ok']); }
    public function simulatePrice(Request $request) {
        return response()->json(['margin' => $request->sell - $request->buy]);
    }
}

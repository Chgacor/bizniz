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
        // 1. Tentukan Periode (Bulan Ini)
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // 2. Hitung Omzet (Total Penjualan Kotor)
        $revenue = Transaction::whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        // 3. Hitung HPP (Modal Barang Terjual)
        // Logika: Ambil semua item yang terjual bulan ini, kalikan qty dengan harga beli produk
        $cogs = TransactionItem::whereHas('transaction', function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        })
            ->with('product')
            ->get()
            ->sum(function($item) {
                // Jika produk sudah dihapus, anggap modal 0 (safety check)
                $buyPrice = $item->product ? $item->product->buy_price : 0;
                return $buyPrice * $item->quantity;
            });

        // 4. Hitung Laba Kotor (Gross Profit)
        $grossProfit = $revenue - $cogs;

        return view('finance.index', compact('revenue', 'cogs', 'grossProfit'));
    }

    // Placeholder untuk fitur masa depan (Simpan History Kalkulasi)
    public function getHistory() { return response()->json([]); }
    public function saveCalculation(Request $request) { return response()->json(['status'=>'ok']); }
    public function simulatePrice(Request $request) {
        return response()->json(['margin' => $request->sell - $request->buy]);
    }
}

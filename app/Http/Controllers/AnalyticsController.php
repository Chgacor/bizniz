<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startMonth = $now->copy()->startOfMonth();
        $endMonth = $now->copy()->endOfMonth();

        // 1. DATA KEUANGAN (Pindahan dari FinanceController)
        $monthRevenue = Transaction::whereBetween('created_at', [$startMonth, $endMonth])->sum('total_amount');

        // Hitung Modal (HPP) Bulan Ini
        $monthCogs = TransactionItem::whereHas('transaction', function($q) use ($startMonth, $endMonth) {
            $q->whereBetween('created_at', [$startMonth, $endMonth]);
        })->get()->sum(function($item) {
            // Ambil harga beli saat ini (atau dari history jika ada, kita pakai harga beli produk saat ini untuk simpel)
            return $item->quantity * ($item->product->buy_price ?? 0);
        });

        $monthProfit = $monthRevenue - $monthCogs;


        // 2. DATA ANALITIK LAINNYA
        $todayRevenue = Transaction::whereDate('created_at', Carbon::today())->sum('total_amount');
        $todayOrders = Transaction::whereDate('created_at', Carbon::today())->count();
        $lowStockCount = Product::where('stock_quantity', '<=', 5)->count();

        // 3. TOP PRODUCTS
        $topProducts = TransactionItem::select('product_id', DB::raw('sum(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product')
            ->get();

        // 4. TRANSAKSI TERAKHIR
        $recentTransactions = Transaction::with('user')->latest()->take(5)->get();

        return view('analytics.index', compact(
            'todayRevenue', 'todayOrders', 'lowStockCount',
            'monthRevenue', 'monthCogs', 'monthProfit', // Variabel Keuangan
            'topProducts', 'recentTransactions'
        ));
    }
}

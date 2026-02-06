<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER TANGGAL
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        $period = $request->period ?? 'daily';

        // --- A. REVENUE & PROFIT REPORT ---
        $revenue = Transaction::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalTrx = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();

        // UPDATE: Pisahkan Barang vs Jasa
        $goodsSold = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('products.type', 'goods')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->sum('quantity');

        $servicesBooked = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('products.type', 'service')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->sum('quantity');

        // Hitung Profit
        $grossProfit = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->sum(DB::raw('(transaction_items.price_at_sale - products.buy_price) * transaction_items.quantity'));

        $summary = [
            'revenue' => $revenue,
            'transactions' => $totalTrx,
            'goods_sold' => $goodsSold,       // Data Baru
            'services_booked' => $servicesBooked, // Data Baru
            'net_profit' => $grossProfit,
        ];

        // --- B. STOCK BALANCE ---
        $stockBalance = Product::where('type', 'goods')
            ->select(
                DB::raw('SUM(stock_quantity) as total_stock_qty'),
                DB::raw('SUM(stock_quantity * buy_price) as total_asset_value')
            )
            ->first();

        // --- C. LOW STOCK ---
        $lowStockItems = Product::where('type', 'goods')
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // --- D. GRAFIK ---
        $dateFormat = match($period) {
            'hourly' => '%Y-%m-%d %H:00:00',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };

        $chartQuery = TransactionItem::select(
            DB::raw("DATE_FORMAT(transaction_items.created_at, '$dateFormat') as date_label"),
            DB::raw('SUM(transaction_items.quantity * transaction_items.price_at_sale) as revenue'),
            DB::raw('SUM((transaction_items.price_at_sale - products.buy_price) * transaction_items.quantity) as profit')
        )
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->groupBy('date_label')
            ->get();

        $labels = [];
        $dataRevenue = [];
        $dataProfit = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = match($period) {
                'hourly' => $current->format('Y-m-d H:00:00'),
                'weekly' => $current->format('Y-W'),
                'monthly' => $current->format('Y-m'),
                'yearly' => $current->format('Y'),
                default => $current->format('Y-m-d'),
            };

            $labelDisplay = match($period) {
                'hourly' => $current->format('H:00'),
                'weekly' => 'W'.$current->format('W'),
                'monthly' => $current->format('M Y'),
                'yearly' => $current->format('Y'),
                default => $current->format('d M'),
            };

            $labels[] = $labelDisplay;
            $dataRevenue[] = $chartQuery->where('date_label', $key)->sum('revenue');
            $dataProfit[] = $chartQuery->where('date_label', $key)->sum('profit');

            match($period) {
                'hourly' => $current->addHour(),
                'weekly' => $current->addWeek(),
                'monthly' => $current->addMonth(),
                'yearly' => $current->addYear(),
                default => $current->addDay(),
            };
        }

        $chartData = [
            'labels' => $labels,
            'revenue' => $dataRevenue,
            'profit' => $dataProfit
        ];

        // --- E. PRODUCT BREAKDOWN (UPDATE: Include Type) ---
        $productBreakdown = TransactionItem::select(
            'products.name',
            'products.product_code',
            'products.type', // PENTING: Ambil tipe produk
            DB::raw('SUM(transaction_items.quantity) as total_qty'),
            DB::raw('SUM(transaction_items.quantity * transaction_items.price_at_sale) as total_revenue')
        )
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.product_code', 'products.type')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('analytics.index', compact(
            'startDate', 'endDate', 'period',
            'summary', 'stockBalance', 'lowStockItems',
            'chartData', 'productBreakdown'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'daily';

        // 1. SETUP TANGGAL
        if ($period === 'hourly') {
            // KHUSUS PER JAM: Ambil 1 Hari Full (00:00 - 23:59) dari Start Date
            $date = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now();
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        } else {
            // PERIODE LAIN: Normal range
            $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        }

        // --- A. SUMMARY REPORT ---
        $revenue = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalTrx = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        // Hitung Modal (HPP)
        $totalCost = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->sum(DB::raw('products.buy_price * transaction_items.quantity'));

        // Volume Barang vs Jasa
        $goodsSold = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->where('products.type', 'goods')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->sum('quantity');

        $servicesBooked = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->where('products.type', 'service')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->sum('quantity');

        $summary = [
            'revenue' => $revenue,
            'cost' => $totalCost,
            'transactions' => $totalTrx,
            'goods_sold' => $goodsSold,
            'services_booked' => $servicesBooked,
            'net_profit' => $revenue - $totalCost,
        ];

        // --- B. STOCK & LOW STOCK ---
        $stockBalance = Product::where('type', 'goods')
            ->selectRaw('SUM(stock_quantity) as total_stock_qty, SUM(stock_quantity * buy_price) as total_asset_value')
            ->first();

        $lowStockItems = Product::where('type', 'goods')
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // --- C. DATA GRAFIK (CHART) ---
        // Tentukan Format Grouping SQL
        $dateFormat = match($period) {
            'hourly' => '%Y-%m-%d %H:00:00', // Group per jam
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };

        // Query Data Grafik
        $chartQuery = TransactionItem::select(
            DB::raw("DATE_FORMAT(transaction_items.created_at, '$dateFormat') as date_label"),
            DB::raw('SUM(transaction_items.quantity * transaction_items.price_at_sale) as revenue'),
            DB::raw('SUM((transaction_items.price_at_sale - products.buy_price) * transaction_items.quantity) as profit')
        )
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->groupBy('date_label')
            ->get();

        // Siapkan Loop Data (Agar grafik tidak bolong)
        $labels = [];
        $dataRevenue = [];
        $dataProfit = [];

        $current = $startDate->copy(); // Mulai dari 00:00 jika Hourly

        while ($current <= $endDate) {
            // Key Pencocokan Data
            $key = match($period) {
                'hourly' => $current->format('Y-m-d H:00:00'),
                'weekly' => $current->format('Y-W'),
                'monthly' => $current->format('Y-m'),
                'yearly' => $current->format('Y'),
                default => $current->format('Y-m-d'),
            };

            // Label Tampilan di Grafik
            $labelDisplay = match($period) {
                'hourly' => $current->format('H:00'), // Tampil Jam: 00:00, 01:00...
                'weekly' => 'W'.$current->format('W'),
                'monthly' => $current->format('M Y'),
                'yearly' => $current->format('Y'),
                default => $current->format('d M'),
            };

            $labels[] = $labelDisplay;

            // Ambil data (atau 0 jika kosong)
            $row = $chartQuery->where('date_label', $key)->first();
            $dataRevenue[] = $row ? $row->revenue : 0;
            $dataProfit[] = $row ? $row->profit : 0;

            // Increment Loop
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

        // --- D. PRODUCT BREAKDOWN ---
        $productBreakdown = TransactionItem::select(
            'products.name',
            'products.product_code',
            'products.type',
            DB::raw('SUM(transaction_items.quantity) as total_qty'),
            DB::raw('SUM(transaction_items.quantity * transaction_items.price_at_sale) as total_revenue')
        )
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.product_code', 'products.type')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('analytics.index', compact(
            'startDate', 'endDate', 'period',
            'summary', 'stockBalance', 'lowStockItems',
            'chartData', 'productBreakdown'
        ));
    }

    public function export(Request $request)
    {
        $period = $request->period ?? 'daily';

        // Logika Tanggal Export sama dengan Index
        if ($period === 'hourly') {
            $date = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now();
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
        } else {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
        }

        $filename = "laporan_bizniz_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Jam', 'Invoice', 'Kasir', 'Pelanggan', 'Total Omset', 'Total Modal', 'Profit']);

            Transaction::with(['user', 'customer', 'items.product'])
                ->where('status', 'completed')
                ->whereBetween('created_at', [$start, $end])
                ->chunk(100, function($transactions) use ($file) {
                    foreach ($transactions as $txn) {
                        $txnCost = 0;
                        foreach($txn->items as $item) {
                            $bp = $item->product ? $item->product->buy_price : 0;
                            $txnCost += ($bp * $item->quantity);
                        }
                        fputcsv($file, [
                            $txn->created_at->format('Y-m-d'),
                            $txn->created_at->format('H:i'),
                            $txn->invoice_code,
                            $txn->user->name,
                            $txn->customer ? $txn->customer->name : 'Umum',
                            $txn->total_amount,
                            $txnCost,
                            $txn->total_amount - $txnCost,
                        ]);
                    }
                });
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

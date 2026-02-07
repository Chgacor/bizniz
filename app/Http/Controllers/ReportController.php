<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Range (Default: This Month)
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();
        $period = $request->input('period', 'daily'); // daily, weekly, monthly

        // 2. Base Query for Transactions within Date Range
        $transactionsQuery = Transaction::with('items.product')
            ->where('status', 'completed') // Only completed transactions
            ->whereBetween('created_at', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')]);

        $transactions = $transactionsQuery->get();

        // 3. Calculate Summary Metrics
        $totalRevenue = 0;
        $totalCost = 0; // Total Modal
        $goodsSold = 0;
        $servicesBooked = 0;

        foreach ($transactions as $trx) {
            $totalRevenue += $trx->total_amount;

            foreach ($trx->items as $item) {
                // Calculate Cost (Modal)
                // If product exists, use its buy_price. If deleted/manual, assume 0 or handle logic.
                $buyPrice = $item->product ? $item->product->buy_price : 0;
                $itemCost = $buyPrice * $item->quantity;
                $totalCost += $itemCost;

                // Count Volumes
                if ($item->product && $item->product->type === 'service') {
                    $servicesBooked += $item->quantity;
                } else {
                    $goodsSold += $item->quantity;
                }
            }
        }

        $netProfit = $totalRevenue - $totalCost;

        $summary = [
            'revenue' => $totalRevenue,
            'cost' => $totalCost, // New Metric
            'net_profit' => $netProfit,
            'transactions' => $transactions->count(),
            'goods_sold' => $goodsSold,
            'services_booked' => $servicesBooked,
        ];

        // 4. Warehouse Asset Value (Current Stock * Buy Price)
        $stockBalance = Product::selectRaw('SUM(stock_quantity * buy_price) as total_asset_value, SUM(stock_quantity) as total_stock_qty')
            ->where('type', 'goods')
            ->first();

        // 5. Low Stock Alert
        $lowStockItems = Product::where('type', 'goods')
            ->where('stock_quantity', '<', 5)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // 6. Product Sales Breakdown (Top 10)
        $productBreakdown = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                'products.name',
                'products.product_code',
                'products.type',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.quantity * transaction_items.price_at_sale) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.product_code', 'products.type')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // 7. Chart Data Preparation
        $chartData = $this->prepareChartData($transactions, $startDate, $endDate, $period);

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'period',
            'summary',
            'stockBalance',
            'lowStockItems',
            'productBreakdown',
            'chartData'
        ));
    }

    private function prepareChartData($transactions, $start, $end, $period)
    {
        // Group transactions by date format based on period
        $grouped = $transactions->groupBy(function($item) use ($period) {
            if ($period == 'monthly') return $item->created_at->format('Y-m'); // 2024-02
            if ($period == 'yearly') return $item->created_at->format('Y');    // 2024
            return $item->created_at->format('d M'); // 12 Feb (Daily default)
        });

        $labels = [];
        $revenueData = [];
        $profitData = [];

        // Sort keys to ensure chronological order on chart
        $keys = $grouped->keys()->sort();

        foreach ($keys as $key) {
            $trxs = $grouped[$key];
            $rev = $trxs->sum('total_amount');

            // Calculate cost for this group
            $cost = 0;
            foreach ($trxs as $t) {
                foreach($t->items as $i) {
                    $bp = $i->product ? $i->product->buy_price : 0;
                    $cost += ($bp * $i->quantity);
                }
            }

            $labels[] = $key;
            $revenueData[] = $rev;
            $profitData[] = $rev - $cost;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'profit' => $profitData
        ];
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $filename = "bizniz_sales_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($start, $end) {
            $file = fopen('php://output', 'w');

            // CSV Header Row (Added 'Total Cost' and 'Profit')
            fputcsv($file, ['Date', 'Invoice', 'Cashier', 'Customer', 'Items Count', 'Total Amount', 'Total Cost (Modal)', 'Profit', 'Cash Received', 'Change']);

            // Stream Data
            Transaction::with(['user', 'customer', 'items.product'])
                ->whereBetween('created_at', [$start, $end])
                ->chunk(100, function($transactions) use ($file) {
                    foreach ($transactions as $txn) {

                        // Calculate Cost per Transaction
                        $txnCost = 0;
                        foreach($txn->items as $item) {
                            $buyPrice = $item->product ? $item->product->buy_price : 0;
                            $txnCost += ($buyPrice * $item->quantity);
                        }
                        $txnProfit = $txn->total_amount - $txnCost;

                        fputcsv($file, [
                            $txn->created_at->format('Y-m-d H:i'),
                            $txn->invoice_code,
                            $txn->user->name,
                            $txn->customer ? $txn->customer->name : 'Guest',
                            $txn->items->count(),
                            $txn->total_amount,
                            $txnCost,      // Modal
                            $txnProfit,    // Profit
                            $txn->cash_received,
                            $txn->change_amount ?? 0 // Handle null change if applicable
                        ]);
                    }
                });

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

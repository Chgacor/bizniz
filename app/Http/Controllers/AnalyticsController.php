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
        // SETTING: Timezone WIB
        $tz = 'Asia/Jakarta';
        $period = $request->period ?? 'daily';

        $dateInput = $request->start_date
            ? Carbon::parse($request->start_date, $tz)
            : Carbon::now($tz);

        // 1. TENTUKAN JENDELA WAKTU
        if ($period === 'hourly') {
            $startDate = $dateInput->copy()->startOfDay();
            $endDate = $dateInput->copy()->endOfDay();
        } elseif ($period === 'monthly') {
            $startDate = $dateInput->copy()->startOfYear();
            $endDate = $dateInput->copy()->endOfYear();
        } elseif ($period === 'yearly') {
            $startDate = $dateInput->copy()->startOfYear();
            $endDate = $dateInput->copy()->addYears(5)->endOfYear();
        } else {
            $startDate = $request->start_date ? Carbon::parse($request->start_date, $tz)->startOfDay() : Carbon::now($tz)->startOfMonth();
            $endDate = $request->end_date ? Carbon::parse($request->end_date, $tz)->endOfDay() : Carbon::now($tz)->endOfMonth();
        }

        // 2. AMBIL DATA
        $items = TransactionItem::with(['product', 'transaction'])
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->where(function($query) {
                    $query->where('status', 'completed')
                        ->orWhereNull('status')
                        ->orWhere('status', '');
                })
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        // 3. HITUNG SUMMARY
        $revenue = 0; $totalCost = 0;
        $goodsSold = 0; $servicesBooked = 0;

        foreach ($items as $item) {
            $revenue += ($item->quantity * $item->price_at_sale);
            $totalCost += ($item->quantity * ($item->product->buy_price ?? 0));

            if ($item->product && $item->product->type === 'service') {
                $servicesBooked += $item->quantity;
            } else {
                $goodsSold += $item->quantity;
            }
        }

        $summary = [
            'revenue' => $revenue,
            'cost' => $totalCost,
            'net_profit' => $revenue - $totalCost,
            'transactions' => $items->unique('transaction_id')->count(),
            'goods_sold' => $goodsSold,
            'services_booked' => $servicesBooked,
        ];

        // 4. GROUPING (Timezone Safe)
        $grouped = $items->groupBy(function($item) use ($period, $tz) {
            $dt = $item->created_at->timezone($tz);
            return match($period) {
                'hourly' => $dt->format('H:00'),
                'monthly' => $dt->format('Y-m'),
                'yearly' => $dt->format('Y'),
                default => $dt->format('Y-m-d'),
            };
        });

        // 5. DATA GRAFIK
        $labels = []; $dataRevenue = []; $dataProfit = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $key = match($period) {
                'hourly' => $current->format('H:00'),
                'monthly' => $current->format('Y-m'),
                'yearly' => $current->format('Y'),
                default => $current->format('Y-m-d'),
            };

            $labels[] = match($period) {
                'hourly' => $current->format('H:00'),
                'monthly' => $current->format('M'),
                'yearly' => $current->format('Y'),
                default => $current->format('d M'),
            };

            if (isset($grouped[$key])) {
                $g = $grouped[$key];
                $r = $g->sum(fn($i) => $i->quantity * $i->price_at_sale);
                $c = $g->sum(fn($i) => $i->quantity * ($i->product->buy_price ?? 0));
                $dataRevenue[] = $r;
                $dataProfit[] = $r - $c;
            } else {
                $dataRevenue[] = 0; $dataProfit[] = 0;
            }

            match($period) {
                'hourly' => $current->addHour(),
                'monthly' => $current->addMonth(),
                'yearly' => $current->addYear(),
                default => $current->addDay(),
            };
        }

        $chartData = ['labels' => $labels, 'revenue' => $dataRevenue, 'profit' => $dataProfit];

        // DATA LAIN
        $stockBalance = Product::where('type', 'goods')->selectRaw('SUM(stock_quantity) as total_stock_qty, SUM(stock_quantity * buy_price) as total_asset_value')->first();
        $lowStockItems = Product::where('type', 'goods')->where('stock_quantity', '<=', 5)->orderBy('stock_quantity', 'asc')->limit(10)->get();

        // --- PERBAIKAN DI SINI (Menambahkan product_code) ---
        $productBreakdown = $items->groupBy('product_id')->map(function($g) {
            $f = $g->first();
            return (object)[
                'name' => $f->product->name ?? $f->name,
                'type' => $f->product->type ?? 'goods',
                'product_code' => $f->product->product_code ?? '-', // <--- INI BARIS PENYELAMAT ERROR
                'total_qty' => $g->sum('quantity'),
                'total_revenue' => $g->sum(fn($i) => $i->quantity * $i->price_at_sale)
            ];
        })->sortByDesc('total_revenue')->take(10);

        return view('analytics.index', compact('startDate', 'endDate', 'period', 'summary', 'stockBalance', 'lowStockItems', 'chartData', 'productBreakdown'));
    }

    // Fungsi Export CSV
    public function export(Request $request) {
        $tz = 'Asia/Jakarta';
        $period = $request->period ?? 'daily';
        $dateInput = $request->start_date ? Carbon::parse($request->start_date, $tz) : Carbon::now($tz);

        if ($period === 'hourly') {
            $start = $dateInput->copy()->startOfDay(); $end = $dateInput->copy()->endOfDay();
        } elseif ($period === 'monthly') {
            $start = $dateInput->copy()->startOfYear(); $end = $dateInput->copy()->endOfYear();
        } elseif ($period === 'yearly') {
            $start = $dateInput->copy()->startOfYear(); $end = $dateInput->copy()->addYears(5)->endOfYear();
        } else {
            $start = Carbon::parse($request->start_date, $tz)->startOfDay();
            $end = Carbon::parse($request->end_date, $tz)->endOfDay();
        }

        $filename = "Laporan_Bisnis_" . date('Ymd_Hi') . ".csv";
        $callback = function() use ($start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Jam', 'Invoice', 'Omset', 'Modal', 'Profit']);
            Transaction::with(['items.product'])
                ->where(function($q) { $q->where('status', 'completed')->orWhereNull('status')->orWhere('status', ''); })
                ->whereBetween('created_at', [$start, $end])
                ->chunk(100, function($txs) use ($file) {
                    foreach ($txs as $tx) {
                        $cst = $tx->items->sum(fn($i) => $i->quantity * ($i->product->buy_price ?? 0));
                        // Paksa WIB saat export
                        $d = $tx->created_at->timezone('Asia/Jakarta');
                        fputcsv($file, [$d->format('Y-m-d'), $d->format('H:i'), $tx->invoice_code, $tx->total_amount, $cst, $tx->total_amount - $cst]);
                    }
                });
            fclose($file);
        };
        return Response::stream($callback, 200, ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"]);
    }
}

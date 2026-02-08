<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $tz = 'Asia/Jakarta';
        $period = $request->period ?? 'daily';
        $dateInput = $request->start_date ? Carbon::parse($request->start_date, $tz) : Carbon::now($tz);

        // 1. SETUP TANGGAL
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

        // 2. DATA TRANSAKSI
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

        // 3. SUMMARY
        $revenue = $items->sum(fn($i) => $i->quantity * $i->price_at_sale);
        $totalCost = $items->sum(fn($i) => $i->quantity * ($i->product->buy_price ?? 0));

        $summary = [
            'revenue' => $revenue,
            'cost' => $totalCost,
            'net_profit' => $revenue - $totalCost,
            'transactions' => $items->unique('transaction_id')->count(),
            'goods_sold' => $items->where('product.type', 'goods')->sum('quantity'),
            'services_booked' => $items->where('product.type', 'service')->sum('quantity'),
        ];

        // 4. CHART DATA (PHP Grouping)
        $grouped = $items->groupBy(function($item) use ($period, $tz) {
            $dt = $item->created_at->timezone($tz);
            return match($period) {
                'hourly' => $dt->format('H:00'),
                'monthly' => $dt->format('Y-m'),
                'yearly' => $dt->format('Y'),
                default => $dt->format('Y-m-d'),
            };
        });

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

        // 5. ASET GUDANG (UPDATE: HPP & HET)
        $stockBalance = Product::where('type', 'goods')
            ->selectRaw('
                SUM(stock_quantity) as total_stock_qty,
                SUM(stock_quantity * buy_price) as total_asset_hpp,
                SUM(stock_quantity * sell_price) as total_asset_het
            ')
            ->first();

        $lowStockItems = Product::where('type', 'goods')->where('stock_quantity', '<=', 5)->orderBy('stock_quantity', 'asc')->limit(10)->get();

        $productBreakdown = $items->groupBy('product_id')->map(function($g) {
            $f = $g->first();
            return (object)[
                'name' => $f->product->name ?? $f->name,
                'type' => $f->product->type ?? 'goods',
                'product_code' => $f->product->product_code ?? '-',
                'total_qty' => $g->sum('quantity'),
                'total_revenue' => $g->sum(fn($i) => $i->quantity * $i->price_at_sale)
            ];
        })->sortByDesc('total_revenue')->take(10);

        return view('analytics.index', compact('startDate', 'endDate', 'period', 'summary', 'stockBalance', 'lowStockItems', 'chartData', 'productBreakdown'));
    }

    // EXPORT CSV DENGAN RINGKASAN ASET
    // ... method index tetap sama, jangan diubah ...

    // UPDATE: FUNGSI EXPORT KE EXCEL (.XLS)
    public function export(Request $request)
    {
        $tz = 'Asia/Jakarta';
        $period = $request->period ?? 'daily';
        $dateInput = $request->start_date ? Carbon::parse($request->start_date, $tz) : Carbon::now($tz);

        // 1. TENTUKAN RENTANG WAKTU (SAMA SEPERTI INDEX)
        if ($period === 'hourly') {
            $start = $dateInput->copy()->startOfDay();
            $end = $dateInput->copy()->endOfDay();
        } elseif ($period === 'monthly') {
            $start = $dateInput->copy()->startOfYear();
            $end = $dateInput->copy()->endOfYear();
        } elseif ($period === 'yearly') {
            $start = $dateInput->copy()->startOfYear();
            $end = $dateInput->copy()->addYears(5)->endOfYear();
        } else {
            $start = Carbon::parse($request->start_date, $tz)->startOfDay();
            $end = Carbon::parse($request->end_date, $tz)->endOfDay();
        }

        $filename = "Laporan_Bisnis_" . date('Ymd_Hi') . ".xls"; // Ubah ekstensi jadi .xls

        $headers = [
            "Content-Type" => "application/vnd.ms-excel", // Header khusus Excel
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($start, $end) {
            $file = fopen('php://output', 'w');

            // Hitung Aset Gudang Dulu
            $stock = Product::where('type', 'goods')
                ->selectRaw('SUM(stock_quantity * buy_price) as hpp, SUM(stock_quantity * sell_price) as het, SUM(stock_quantity) as qty')
                ->first();

            // MULAI TULIS HTML UNTUK EXCEL
            // Kita pakai HTML Table biasa, Excel otomatis membacanya sebagai Spreadsheet
            fwrite($file, '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>');

            // --- TABEL 1: RINGKASAN ASET GUDANG ---
            fwrite($file, '<h3>LAPORAN ASET GUDANG (SNAPSHOT LIVE)</h3>');
            fwrite($file, '<table border="1" style="border-collapse: collapse;">');
            fwrite($file, '<tr style="background-color: #f0f0f0;">
                <th style="width: 150px; text-align: left;">Total Unit Stok</th>
                <th style="width: 200px; text-align: left;">Total Nilai Modal (HPP)</th>
                <th style="width: 200px; text-align: left;">Total Nilai Jual (HET)</th>
                <th style="width: 200px; text-align: left;">Potensi Margin</th>
            </tr>');
            fwrite($file, '<tr>
                <td style="text-align: center;">' . ($stock->qty ?? 0) . '</td>
                <td>Rp ' . number_format($stock->hpp ?? 0, 0, ',', '.') . '</td>
                <td>Rp ' . number_format($stock->het ?? 0, 0, ',', '.') . '</td>
                <td style="color: green;">Rp ' . number_format(($stock->het - $stock->hpp) ?? 0, 0, ',', '.') . '</td>
            </tr>');
            fwrite($file, '</table>');
            fwrite($file, '<br><br>'); // Spasi antar tabel

            // --- TABEL 2: DETAIL TRANSAKSI ---
            fwrite($file, '<h3>DETAIL TRANSAKSI PENJUALAN</h3>');
            fwrite($file, '<strong>Periode: ' . $start->format('d M Y') . ' s/d ' . $end->format('d M Y') . '</strong><br><br>');

            fwrite($file, '<table border="1" style="border-collapse: collapse;">');
            fwrite($file, '<tr style="background-color: #d1e7dd;">
                <th style="width: 120px;">Tanggal</th>
                <th style="width: 80px;">Jam</th>
                <th style="width: 150px;">Invoice</th>
                <th style="width: 150px;">Kasir</th>
                <th style="width: 150px;">Omset</th>
                <th style="width: 150px;">Modal (HPP)</th>
                <th style="width: 150px;">Profit</th>
            </tr>');

            Transaction::with(['items.product', 'user'])
                ->where(function($q) { $q->where('status', 'completed')->orWhereNull('status')->orWhere('status', ''); })
                ->whereBetween('created_at', [$start, $end])
                ->chunk(100, function($txs) use ($file) {
                    foreach ($txs as $tx) {
                        $cst = $tx->items->sum(fn($i) => $i->quantity * ($i->product->buy_price ?? 0));
                        $dateWib = $tx->created_at->timezone('Asia/Jakarta');

                        fwrite($file, '<tr>');
                        fwrite($file, '<td style="text-align: center;">' . $dateWib->format('Y-m-d') . '</td>');
                        fwrite($file, '<td style="text-align: center;">' . $dateWib->format('H:i') . '</td>');
                        fwrite($file, '<td>' . $tx->invoice_code . '</td>');
                        fwrite($file, '<td>' . ($tx->user->name ?? 'System') . '</td>');
                        fwrite($file, '<td>' . $tx->total_amount . '</td>');
                        fwrite($file, '<td>' . $cst . '</td>');
                        fwrite($file, '<td style="font-weight: bold;">' . ($tx->total_amount - $cst) . '</td>');
                        fwrite($file, '</tr>');
                    }
                });

            fwrite($file, '</table>');
            fwrite($file, '</body></html>');
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

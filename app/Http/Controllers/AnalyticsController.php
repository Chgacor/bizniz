<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $period    = $request->period ?? 'daily';

        $items = TransactionItem::with(['product', 'transaction'])
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed');
            })
            ->get();

        $chartData = [
            'labels' => [],
            'goods'  => [],
            'services' => []
        ];

        $grouped = $items->groupBy(function($item) use ($period) {
            $date = Carbon::parse($item->created_at);
            return match ($period) {
                'hourly'  => $date->format('d M H:00'),
                'daily'   => $date->format('d M Y'),
                'weekly'  => 'Week ' . $date->weekOfYear,
                'monthly' => $date->format('M Y'),
                'yearly'  => $date->format('Y'),
                default   => $date->format('d M Y'),
            };
        });

        foreach ($grouped as $label => $groupItems) {
            $chartData['labels'][] = $label;
            $totalGoods = 0;
            $totalServices = 0;

            foreach ($groupItems as $item) {
                $price = $item->price_at_sale ?? $item->price ?? 0;
                $subtotal = $price * $item->quantity;
                $isService = ($item->product_id === null) ||
                    ($item->product && $item->product->type === 'service');

                if ($isService) {
                    $totalServices += $subtotal;
                } else {
                    $totalGoods += $subtotal;
                }
            }

            $chartData['goods'][] = $totalGoods;
            $chartData['services'][] = $totalServices;
        }

        $topGoods = TransactionItem::select('name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price_at_sale * quantity) as total_revenue'))
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereHas('product', function($q) {
                $q->where('type', 'goods');
            })
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->groupBy('name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topServices = TransactionItem::select('name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price_at_sale * quantity) as total_revenue'))
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where(function($q) {
                $q->whereNull('product_id')
                    ->orWhereHas('product', function($sq) {
                        $sq->where('type', 'service');
                    });
            })
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->groupBy('name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $summary = [
            'total_revenue' => $items->sum(fn($i) => ($i->price_at_sale ?? $i->price ?? 0) * $i->quantity),
            'total_trx'     => $items->pluck('transaction_id')->unique()->count(),
        ];

        return view('analytics.index', compact(
            'chartData', 'topGoods', 'topServices', 'summary', 'startDate', 'endDate', 'period'
        ));
    }
}

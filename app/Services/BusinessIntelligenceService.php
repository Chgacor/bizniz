<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;

class BusinessIntelligenceService
{
    public function getDashboardMetrics()
    {
        $today = Carbon::today();

        return [
            'daily_revenue' => Transaction::whereDate('created_at', $today)->sum('total_amount'),
            'total_transactions' => Transaction::whereDate('created_at', $today)->count(),
            'low_stock_count' => Product::where('stock_quantity', '<', 5)->count(),
        ];
    }

    /**
     * The "AI" Logic: Analyzes patterns and returns structured advice.
     */
    public function generateInsights()
    {
        $insights = [];
        $products = Product::withCount(['movements as total_sold' => function($query){
            $query->where('type', 'sale');
        }])->get();

        foreach ($products as $product) {
            // Logic 1: High Demand, Low Stock (Urgent Restock)
            if ($product->total_sold > 10 && $product->stock_quantity < 5) {
                $insights[] = [
                    'type' => 'critical',
                    'product' => $product->name,
                    'message' => "High Velocity Risk: Product is selling fast but stock is critical.",
                    'action' => "Restock immediately. Consider raising price by 5% to slow depletion.",
                    'confidence' => 'High'
                ];
            }

            // Logic 2: Dead Stock (Capital Freeze)
            if ($product->total_sold == 0 && $product->created_at < Carbon::now()->subMonth()) {
                $insights[] = [
                    'type' => 'warning',
                    'product' => $product->name,
                    'message' => "Capital Freeze: No sales in 30 days.",
                    'action' => "Create a bundle deal or discount to liquidate inventory.",
                    'confidence' => 'Medium'
                ];
            }

            // Logic 3: Margin Opportunity
            $margin = $product->sell_price - $product->buy_price;
            if ($product->total_sold > 20 && $margin < ($product->buy_price * 0.1)) {
                $insights[] = [
                    'type' => 'opportunity',
                    'product' => $product->name,
                    'message' => "Low Margin Leader: High volume sales but profit is thin (<10%).",
                    'action' => "Increase price. Customers are loyal to this item; slight increase won't hurt.",
                    'confidence' => 'High'
                ];
            }
        }

        return collect($insights)->sortByDesc('type'); // Critical first
    }
}

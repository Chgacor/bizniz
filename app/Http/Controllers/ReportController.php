<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    // 1. Show the Filter Page
    public function index()
    {
        return view('reports.index');
    }

    // 2. Generate and Stream the CSV
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

            // CSV Header Row
            fputcsv($file, ['Date', 'Invoice', 'Cashier', 'Customer', 'Items Count', 'Total Amount', 'Cash Received', 'Change']);

            // Stream Data (Chunking to prevent memory overflow)
            Transaction::with(['user', 'customer'])
                ->whereBetween('created_at', [$start, $end])
                ->chunk(100, function($transactions) use ($file) {
                    foreach ($transactions as $txn) {
                        fputcsv($file, [
                            $txn->created_at->format('Y-m-d H:i'),
                            $txn->invoice_code,
                            $txn->user->name,
                            $txn->customer ? $txn->customer->name : 'Guest',
                            $txn->items()->count(),
                            $txn->total_amount,
                            $txn->cash_received,
                            $txn->change_returned
                        ]);
                    }
                });

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

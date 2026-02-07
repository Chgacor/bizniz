<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $tab = $request->input('tab', 'all'); // Default tampilkan 'all' (semua)

        // 1. PENCARIAN TRANSAKSI (Hanya jalan jika ada search box diisi)
        $searchResults = null;
        if ($query) {
            $searchResults = Transaction::with(['items.product', 'user'])
                ->where('invoice_code', 'like', "%{$query}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$query}%"))
                ->orWhereHas('items', fn($q) => $q->where('name', 'like', "%{$query}%"))
                ->latest()
                ->get();
        }

        // 2. LOG PERGERAKAN STOK (Ditambah Logika Filter Tab)
        $stockLogs = StockMovement::with(['product', 'user'])
            ->when($query, function($q) use ($query) {
                // Filter Search
                $q->where('description', 'like', "%{$query}%")
                    ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$query}%"));
            })
            ->when($tab === 'in', function($q) {
                // Filter Tab Masuk
                $q->where('type', 'in');
            })
            ->when($tab === 'out', function($q) {
                // Filter Tab Keluar
                $q->where('type', 'out');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString(); // Agar saat pindah halaman, filter tab tetap terbawa

        return view('history.index', compact('stockLogs', 'searchResults', 'query', 'tab'));
    }
}

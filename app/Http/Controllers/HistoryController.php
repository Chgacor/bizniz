<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\SalesReturn;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->latest()
            ->paginate(15, ['*'], 'movements_page');

        $purchases = PurchaseInvoice::with('user')
            ->latest()
            ->paginate(10, ['*'], 'purchases_page');

        $returns = SalesReturn::with(['transaction', 'user'])
            ->latest()
            ->paginate(10, ['*'], 'returns_page');

        return view('history.index', compact('movements', 'purchases', 'returns'));
    }
}

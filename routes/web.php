<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Models\TransactionItem;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    // 1. DASHBOARD (Akses untuk semua user yang login)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================================
    // GROUP A: OPERATIONAL (Staff, Owner, Admin)
    // =========================================================================
    Route::group(['middleware' => ['role:Staff|Owner|Admin']], function () {

        // 1. Warehouse
        Route::resource('warehouse', WarehouseController::class);

        // 2. Point of Sale (POS)
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search'); // Search Produk
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/transaction/{invoice_code}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');

        // 3. Customers
        // --- PERBAIKAN: Taruh route spesifik DI ATAS Resource ---
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::post('/customers/quick-store', [CustomerController::class, 'storeFromPos'])->name('customers.quick_store');

        // Resource goes LAST because it contains a wildcard path ({id})
        Route::resource('customers', CustomerController::class);
    });

    // =========================================================================
    // GROUP B: INTELLIGENCE & REPORTING (Viewer, Owner, Admin)
    // Akses: Analitik, Keuangan, Laporan (View Only)
    // =========================================================================
    Route::group(['middleware' => ['role:Viewer|Owner|Admin']], function () {

        // Business Intelligence Dashboard
        // Analitik sekarang jadi PUSAT SEMUANYA
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

        // Route untuk Action Export (Form-nya ada di halaman Analitik)
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        // Finance Tools

    });

    Route::get('/fix-prices', function() {
        // 1. Cari item transaksi yang harganya 0 atau NULL
        $items = TransactionItem::where('price_at_sale', '<=', 0)
            ->orWhereNull('price_at_sale')
            ->get();

        $count = 0;
        foreach($items as $item) {
            // 2. Cari produk aslinya di gudang
            $product = Product::find($item->product_id);

            if($product) {
                // 3. Update harga transaksi mengikuti harga jual produk saat ini
                $item->update([
                    'price_at_sale' => $product->sell_price,
                    // Kita update juga kolom 'price' biar aman
                    'price' => $product->sell_price
                ]);
                $count++;
            }
        }

        return "SIAP PAK BOS! Berhasil memperbaiki $count data transaksi yang harganya Rp 0. Silakan cek menu Analitik sekarang.";
    });

    // =========================================================================
    // GROUP C: STRATEGIC & ADMIN (Owner, Admin)
    // Akses: Manajemen User, Simulasi Harga, Pengaturan Sistem, Backup
    // =========================================================================
    Route::group(['middleware' => ['role:Owner|Admin']], function () {
        Route::resource('users', UserController::class);

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/settings/backup', [SettingController::class, 'downloadBackup'])->name('settings.backup');
    });

    // Notifikasi (Mark as Read)
    Route::get('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications->where('id', $id)->markAsRead();
        return back();
    })->name('notifications.read');

});

require __DIR__.'/auth.php';

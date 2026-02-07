<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\HistoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Manajemen Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================================
    // GROUP A: OPERASIONAL (Staff, Owner, Admin)
    // Akses: Kasir, Gudang, Pelanggan, Retur
    // =========================================================================
    Route::group(['middleware' => ['role:Staff|Owner|Admin']], function () {

        // 1. Point of Sale (POS)
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store'); // Simpan Transaksi
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::get('/transaction/{invoice_code}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/print/{invoice_code}', [PosController::class, 'receipt'])->name('pos.print'); // Alias print

        // 2. Warehouse / Gudang
        Route::resource('warehouse', WarehouseController::class);

        // 3. Customers / Pelanggan
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::post('/customers/quick-store', [CustomerController::class, 'storeFromPos'])->name('customers.quick_store');
        Route::resource('customers', CustomerController::class);

        // 4. Retur Barang
        Route::get('/returns/history', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
        Route::get('/returns/search', [ReturnController::class, 'searchTransaction'])->name('returns.search');
        Route::post('/returns/store', [ReturnController::class, 'store'])->name('returns.store');
    });

    // =========================================================================
    // GROUP B: LAPORAN & ANALITIK (Viewer, Owner, Admin)
    // Akses: Melihat Grafik, Riwayat Log, Export Data
    // =========================================================================
    Route::group(['middleware' => ['role:Viewer|Owner|Admin']], function () {

        // Analitik Bisnis
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::post('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');

        // Pusat Riwayat & Log (Pencarian Global)
        Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

        // Export Laporan Umum
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // =========================================================================
    // GROUP C: ADMIN & STRATEGIS (Owner, Admin)
    // Akses: User, Setting, Pembelian Stok (Kulakan), Promo
    // =========================================================================
    Route::group(['middleware' => ['role:Owner|Admin']], function () {

        // 1. Manajemen User
        Route::resource('users', UserController::class);

        // 2. Pengaturan Sistem
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/backup', [SettingController::class, 'downloadBackup'])->name('settings.backup');

        // 3. Pembelian Stok (Restock/Kulakan)
        Route::get('/purchase/history', [PurchaseController::class, 'index'])->name('purchase.index');
        Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
        Route::get('/purchase/search-products', [PurchaseController::class, 'searchProducts'])->name('purchase.search');

        // 4. Promo & Diskon
        Route::resource('promotions', PromotionController::class);
        Route::patch('/promotions/{promotion}/toggle', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle');
    });

    // Utilitas: Notifikasi
    Route::get('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications->where('id', $id)->markAsRead();
        return back();
    })->name('notifications.read');

});

require __DIR__.'/auth.php';

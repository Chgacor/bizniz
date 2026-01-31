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
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search'); // Search Pelanggan
        Route::post('/customers/quick-store', [CustomerController::class, 'storeFromPos'])->name('customers.quick_store');

        // Resource ditaruh PALING BAWAH
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

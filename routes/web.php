<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\ProductVariantController;
use App\Http\Controllers\Penjual\StockInController;
use App\Http\Controllers\Penjual\SaleController;
use App\Http\Controllers\Penjual\OrderTrackingController;
use App\Http\Controllers\Penjual\RevenueReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Penjual\DashboardController as PenjualDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome'); // Halaman welcome default Laravel
    return redirect()->route('login'); // Langsung arahkan ke login jika tidak ada halaman publik
});

// Rute-rute yang memerlukan autentikasi (sudah login)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard umum (mengarahkan berdasarkan role)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'penjual') {
            return redirect()->route('penjual.dashboard');
        }
        // Fallback jika ada role lain atau tidak spesifik (seharusnya tidak terjadi jika role hanya 2)
        return view('dashboard'); // View 'dashboard' bawaan Breeze
    })->name('dashboard');

    // Rute profil dari Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ==================================================================
    // == GRUP RUTE UNTUK SUPER ADMIN ==
    // ==================================================================
    Route::middleware(['role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen User menggunakan Route::resource
        Route::resource('users', AdminUserController::class);

        // Placeholder untuk fitur admin spesifik jika ada (belum diimplementasikan controllernya)
        Route::get('/products-admin', function () { return 'Daftar Produk (versi Admin) - Placeholder'; })->name('products.index.admin');
        Route::get('/revenue-report-detailed', function () { return 'Laporan Revenue Detail (Admin) - Placeholder'; })->name('revenue.report.detailed');
    });


    // ==================================================================
    // == GRUP RUTE UNTUK PENJUAL (DAN SEKARANG JUGA SUPER ADMIN) ==
    // ==================================================================
    // PERUBAHAN DI BARIS BERIKUT: ['role:penjual'] menjadi ['role:superadmin,penjual']
    Route::middleware(['role:superadmin,penjual'])->prefix('penjual')->name('penjual.')->group(function () {
        Route::get('/dashboard', [PenjualDashboardController::class, 'index'])->name('dashboard');

        // Transaksi Pembelian (Stok Keluar)
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        // Jika perlu halaman index/show untuk sales oleh penjual, tambahkan di sini

        // Update Stok Sabun (Stok Masuk)
        Route::get('/stock-ins/create', [StockInController::class, 'create'])->name('stockins.create');
        Route::post('/stock-ins', [StockInController::class, 'store'])->name('stockins.store');
        // Jika perlu halaman index untuk histori stock-ins, tambahkan di sini

        // Update Status Pembayaran (Order Tracking)
        Route::get('/orders', [OrderTrackingController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{sale}/update-status', [OrderTrackingController::class, 'updateStatus'])->name('orders.update_status');
        // Route::get('/orders/{sale}/edit-status', [OrderTrackingController::class, 'edit'])->name('orders.edit_status'); // Opsional

        // Rekap Revenue Penjual
        Route::get('/revenue-report', [RevenueReportController::class, 'index'])->name('revenue.report');
        Route::get('/revenue-report/export', [RevenueReportController::class, 'export'])->name('revenue.export');
    });


    // ==================================================================
    // == GRUP RUTE SHARED (SUPER ADMIN & PENJUAL) ==
    // ==================================================================
    Route::middleware(['role:superadmin,penjual'])->prefix('inventory')->name('inventory.')->group(function () {
        // Manajemen Produk (CRUD lengkap via resource controller)
        Route::resource('products', ProductController::class);

        // Manajemen Varian Produk (terkait dengan produk tertentu untuk create/store, dan mandiri untuk edit/update/destroy)
        Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('variants.create');
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::get('/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('variants.edit');
        Route::put('/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
        // Route::get('/variants/{variant}', [ProductVariantController::class, 'show'])->name('variants.show'); // Jika perlu halaman detail varian
    });

}); // Akhir dari grup middleware ['auth', 'verified']


// Ini penting untuk rute-rute autentikasi dari Breeze (login, register, dll.)
require __DIR__.'/auth.php';

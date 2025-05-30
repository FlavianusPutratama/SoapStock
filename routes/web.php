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
    return redirect()->route('login'); // Atau langsung arahkan ke login jika tidak ada halaman publik
});

// Rute-rute yang memerlukan autentikasi (sudah login)
// Breeze biasanya sudah membuat grup seperti ini atau serupa
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard umum (akan kita modifikasi untuk redirect berdasarkan role)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role == 'superadmin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'penjual') {
            return redirect()->route('penjual.dashboard');
        }
        // Fallback jika ada role lain atau tidak spesifik
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
        Route::get('/dashboard', function () {
            return 'HALAMAN DASHBOARD SUPER ADMIN'; // Placeholder
        })->name('dashboard');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen User menggunakan Route::resource
        // Ini akan otomatis membuat rute untuk index, create, store, show, edit, update, destroy
        Route::resource('users', AdminUserController::class);
        // Route::resource akan menghasilkan nama rute seperti:
        // admin.users.index, admin.users.create, admin.users.store, dll.

        // ... (rute admin lainnya yang sudah kita definisikan sebelumnya)
        Route::get('/products-admin', function () { return 'Daftar Produk (versi Admin)'; })->name('products.index.admin');
        Route::get('/revenue-report-detailed', function () { return 'Laporan Revenue Detail (Admin)'; })->name('revenue.report.detailed');
    });


    // ==================================================================
    // == GRUP RUTE UNTUK PENJUAL ==
    // ==================================================================
    Route::middleware(['role:penjual'])->prefix('penjual')->name('penjual.')->group(function () {
        Route::get('/dashboard', function () {
            return 'HALAMAN DASHBOARD PENJUAL'; // Placeholder
        })->name('dashboard');

        Route::get('/dashboard', [PenjualDashboardController::class, 'index'])->name('dashboard');

        // Transaksi Pembelian (Stok Keluar)
        // (KAYANYA DIHAPUS GASI?) Route::get('/sales/create', function () { return 'Form Transaksi Pembelian Baru (Penjual)'; })->name('sales.create');
        // (KAYANYA DIHAPUS GASI?) Route::post('/sales', function () { return 'Proses Simpan Transaksi Pembelian (Penjual)'; })->name('sales.store');

        // Transaksi Pembelian (Stok Keluar)
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

        // Update Stok Sabun (Stok Masuk)
        // (KAYANYA DIHAPUS GASI?) Route::get('/stock-ins/create', function () { return 'Form Stok Masuk Baru (Penjual)'; })->name('stockins.create');
        // (KAYANYA DIHAPUS GASI?) Route::post('/stock-ins', function () { return 'Proses Simpan Stok Masuk (Penjual)'; })->name('stockins.store');

        // Update Stok Sabun (Stok Masuk)
        Route::get('/stock-ins/create', [StockInController::class, 'create'])->name('stockins.create');
        Route::post('/stock-ins', [StockInController::class, 'store'])->name('stockins.store');

        // Update Status Pembayaran (Order Tracking)
        // (KAYANYA DIHAPUS GASI?) Route::get('/orders', function () { return 'Daftar Order untuk Update Status (Penjual)'; })->name('orders.index');
        // (KAYANYA DIHAPUS GASI?) Route::patch('/orders/{sale}/update-status', function ($sale) { return "Proses Update Status Order ID: {$sale} (Penjual)"; })->name('orders.update_status');

        // Update Status Pembayaran (Order Tracking)
        Route::get('/orders', [OrderTrackingController::class, 'index'])->name('orders.index');
        // Route::get('/orders/{sale}/edit-status', [OrderTrackingController::class, 'edit'])->name('orders.edit_status'); // Opsional jika pakai halaman edit terpisah
        Route::patch('/orders/{sale}/update-status', [OrderTrackingController::class, 'updateStatus'])->name('orders.update_status');

        // Rekap Revenue Penjual
        // (KAYANYA DIHAPUS GASI?) Route::get('/revenue-report', function () { return 'Halaman Rekap Revenue (Penjual)'; })->name('revenue.report');
        // (KAYANYA DIHAPUS GASI?) Route::get('/revenue-report/export', function () { return 'Proses Export Rekap Revenue ke Excel (Penjual)'; })->name('revenue.export');

        // Rekap Revenue Penjual
        Route::get('/revenue-report', [RevenueReportController::class, 'index'])->name('revenue.report');
        Route::get('/revenue-report/export', [RevenueReportController::class, 'export'])->name('revenue.export');

    });


    // ==================================================================
    // == GRUP RUTE SHARED (SUPER ADMIN & PENJUAL) ==
    // == Contoh: Manajemen Produk & Varian jika Penjual juga bisa ==
    // ==================================================================
    Route::middleware(['role:superadmin,penjual'])->prefix('inventory')->name('inventory.')->group(function () {
        // Manajemen Produk
        Route::resource('products', ProductController::class);
        // (MASIH BLM YAKIN DIHAPUS APA NGGA) Route::get('/products', function () { return 'Daftar Produk (Shared)'; })->name('products.index');
        // (MASIH BLM YAKIN DIHAPUS APA NGGA) Route::get('/products/create', function () { return 'Form Tambah Produk (Shared)'; })->name('products.create');
        // (MASIH BLM YAKIN DIHAPUS APA NGGA) Route::post('/products', function () { return 'Proses Simpan Produk Baru (Shared)'; })->name('products.store');
        // (MASIH BLM YAKIN DIHAPUS APA NGGA) Route::get('/products/{product}/edit', function ($product) { return "Form Edit Produk ID: {$product} (Shared)"; })->name('products.edit');
        // (MASIH BLM YAKIN DIHAPUS APA NGGA) Route::put('/products/{product}', function ($product) { return "Proses Update Produk ID: {$product} (Shared)"; })->name('products.update');
        // (MASIH BLM YAKIN DIHAPUS APA NGGA) Route::delete('/products/{product}', function ($product) { return "Proses Hapus Produk ID: {$product} (Shared)"; })->name('products.destroy');
        Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('variants.create');
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::get('/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('variants.edit');
        Route::put('/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
        // Nanti: Route::resource('products', ProductController::class);

        // Manajemen Varian Produk (biasanya terkait dengan produk tertentu)
        // Contoh: route untuk menambah varian ke produk ID tertentu
        Route::get('/products/{product}/variants/create', function ($product) { return "Form Tambah Varian untuk Produk ID: {$product} (Shared)"; })->name('variants.create');
        Route::post('/products/{product}/variants', function ($product) { return "Proses Simpan Varian untuk Produk ID: {$product} (Shared)"; })->name('variants.store');
        Route::get('/variants/{variant}/edit', function ($variant) { return "Form Edit Varian ID: {$variant} (Shared)"; })->name('variants.edit'); // Perlu cara identifikasi produknya juga
        Route::put('/variants/{variant}', function ($variant) { return "Proses Update Varian ID: {$variant} (Shared)"; })->name('variants.update');
        Route::delete('/variants/{variant}', function ($variant) { return "Proses Hapus Varian ID: {$variant} (Shared)"; })->name('variants.destroy');
        // Nanti: Route::resource('products.variants', ProductVariantController::class)->shallow(); atau cara lain
    });

}); // Akhir dari grup middleware ['auth', 'verified']


// Ini penting untuk rute-rute autentikasi dari Breeze (login, register, dll.)
require __DIR__.'/auth.php';
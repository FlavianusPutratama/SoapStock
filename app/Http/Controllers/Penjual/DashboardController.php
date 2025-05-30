<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller // Nama class saya sesuaikan
{
    public function index()
    {
        $userId = Auth::id();

        $todaySalesCount = Sale::where('user_id', $userId)
                               ->whereDate('sale_date', Carbon::today())
                               ->where('payment_status', 'Sudah Dibayar')
                               ->count();
        $todaySalesAmount = Sale::where('user_id', $userId)
                               ->whereDate('sale_date', Carbon::today())
                               ->where('payment_status', 'Sudah Dibayar')
                               ->sum('total_amount_sold');

        $pendingOrdersCount = Sale::where('user_id', $userId)
                                  ->where('payment_status', 'Belum Dibayar')
                                  ->count();

        $lowStockVariants = ProductVariant::where('current_stock', '<=', 5)
                                        ->orderBy('current_stock', 'asc')
                                        ->with('product')
                                        ->take(5)
                                        ->get();

        return view('penjual.dashboard', compact( // Ganti ini
            'todaySalesCount', 'todaySalesAmount', 'pendingOrdersCount', 'lowStockVariants'
        ));
        // Sebelumnya: return "HALAMAN DASHBOARD PENJUAL (Akan diganti view) - Sales Hari Ini: {$todaySalesCount}, Pending Orders: {$pendingOrdersCount}";
    }
}
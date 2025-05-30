<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;

class DashboardController extends Controller // Nama class saya sesuaikan dengan nama file
{
    public function index()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $todaySales = Sale::whereDate('sale_date', Carbon::today())
                            ->where('payment_status', 'Sudah Dibayar')
                            ->sum('total_amount_sold');
        $monthlySales = Sale::whereMonth('sale_date', Carbon::now()->month)
                            ->whereYear('sale_date', Carbon::now()->year)
                            ->where('payment_status', 'Sudah Dibayar')
                            ->sum('total_amount_sold');
        $totalPendingOrders = Sale::where('payment_status', 'Belum Dibayar')->count();

         return view('admin.dashboard', compact(
            'totalUsers', 'totalProducts', 'todaySales', 'monthlySales', 'totalPendingOrders'
        ));
    }
}   
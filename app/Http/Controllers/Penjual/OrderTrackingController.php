<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderTrackingController extends Controller
{
    /**
     * Display a listing of the sales, filterable by payment_status.
     */
    public function index(Request $request)
    {
        $allowedStatuses = ['Belum Dibayar', 'Sudah Dibayar', 'Dibatalkan'];
        $filterStatus = $request->input('status', 'Belum Dibayar'); // Default filter

        if (!in_array($filterStatus, $allowedStatuses)) {
            $filterStatus = 'Belum Dibayar'; // Fallback ke default jika status tidak valid
        }

        // Ambil penjualan berdasarkan status, urutkan dari yang terbaru
        // Tambahkan paginasi jika datanya banyak
        $sales = Sale::where('payment_status', $filterStatus)
                     ->orderBy('sale_date', 'desc')
                     ->with('user', 'items.productVariant.product') // Eager load relasi
                     ->paginate(15); // Contoh paginasi
        
        return view('penjual.orders.index', compact('sales', 'filterStatus', 'allowedStatuses'));
    }

    /**
     * Show the form for editing the specified sale's payment status.
     * (Opsional, bisa langsung update dari halaman index atau modal)
     */
    public function edit(Sale $sale)
    {
        $allowedStatuses = ['Belum Dibayar', 'Sudah Dibayar', 'Dibatalkan'];
        // return view('penjual.orders.edit', compact('sale', 'allowedStatuses'));
        return "Form Edit Status Pembayaran untuk Order ID: {$sale->id} (Akan diganti view)";
    }


    /**
     * Update the payment status of the specified sale.
     */
    public function updateStatus(Request $request, Sale $sale) // Ubah nama method agar lebih spesifik
    {
        $allowedStatuses = ['Belum Dibayar', 'Sudah Dibayar', 'Dibatalkan'];

        $validated = $request->validate([
            'payment_status' => ['required', 'string', Rule::in($allowedStatuses)],
        ]);

        $sale->payment_status = $validated['payment_status'];
        // Anda mungkin ingin mencatat siapa yang mengubah status dan kapan
        // $sale->status_updated_by = Auth::id();
        // $sale->status_updated_at = now();
        $sale->save();

        // return redirect()->route('penjual.orders.index', ['status' => $sale->payment_status])->with('success', "Status pembayaran untuk Order ID: {$sale->id} berhasil diperbarui menjadi '{$sale->payment_status}'.");
        return "Status Pembayaran untuk Order ID: {$sale->id} BERHASIL diupdate menjadi '{$sale->payment_status}'. Redirecting...";
    }
}
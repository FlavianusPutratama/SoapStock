<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\Auth; // Uncomment jika Anda mencatat siapa yang update

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
    public function edit(Sale $sale) // Method ini tidak terpakai jika update langsung dari index
    {
        $allowedStatuses = ['Belum Dibayar', 'Sudah Dibayar', 'Dibatalkan'];
        // Jika Anda membuat view terpisah untuk edit:
        // return view('penjual.orders.edit', compact('sale', 'allowedStatuses'));
        // Untuk sekarang, placeholder ini tidak masalah karena tidak dipanggil dari route yang aktif
        return "Form Edit Status Pembayaran untuk Order ID: {$sale->id} (Akan diganti view)";
    }


    /**
     * Update the payment status of the specified sale.
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $allowedStatuses = ['Belum Dibayar', 'Sudah Dibayar', 'Dibatalkan'];

        $validated = $request->validate([
            'payment_status' => ['required', 'string', Rule::in($allowedStatuses)],
        ]);

        $oldStatus = $sale->payment_status; // Simpan status lama untuk pesan jika perlu
        $sale->payment_status = $validated['payment_status'];
        // Opsional: Catat siapa dan kapan status diubah
        // $sale->updated_by = Auth::id(); // Jika ada kolom updated_by di tabel sales
        // $sale->status_last_updated_at = now(); // Jika ada kolom untuk ini
        $sale->save();

        // ==================================================================
        // == PERUBAHAN DARI PLACEHOLDER KE REDIRECT DENGAN FLASH MESSAGE ==
        // ==================================================================
        return redirect()->back() // Kembali ke halaman sebelumnya (daftar order)
                         ->with('success', "Status pembayaran untuk Order ID: SO-".str_pad($sale->id, 5, '0', STR_PAD_LEFT)." berhasil diperbarui dari '{$oldStatus}' menjadi '{$sale->payment_status}'.");
        // Kode placeholder sebelumnya:
        // return "Status Pembayaran untuk Order ID: {$sale->id} BERHASIL diupdate menjadi '{$sale->payment_status}'. Redirecting...";
    }
}
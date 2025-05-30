<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon; // Penting untuk manipulasi tanggal
// Untuk export Excel, kita akan tambahkan ini nanti saat Tahap 9:
// use App\Exports\RevenueReportExport;
// use Maatwebsite\Excel\Facades\Excel;

class RevenueReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'monthly'); // Default ke bulanan
        $startDateInput = $request->input('start_date'); // Format Y-m-d dari form
        $endDateInput = $request->input('end_date');     // Format Y-m-d dari form

        $query = Sale::where('payment_status', 'Sudah Dibayar')
                     ->with('user', 'items.productVariant.product'); // Eager load jika perlu detail di view

        $reportTitle = '';
        $actualStartDate = null; // Untuk mengisi kembali nilai form filter
        $actualEndDate = null;   // Untuk mengisi kembali nilai form filter

        switch ($period) {
            case 'daily':
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::today();
                $actualStartDate = $date->format('Y-m-d');
                $query->whereDate('sale_date', $date);
                $reportTitle = 'Laporan Harian - ' . $date->isoFormat('D MMMM YYYY');
                break;

            case 'weekly':
                // Jika user memilih tanggal, minggu dihitung dari tanggal tsb
                // Jika tidak, minggu dihitung dari tanggal sekarang
                $dateRef = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $startOfWeek = $dateRef->copy()->startOfWeek(Carbon::MONDAY); // Minggu mulai Senin
                $endOfWeek = $dateRef->copy()->endOfWeek(Carbon::SUNDAY);     // Minggu berakhir Minggu
                $actualStartDate = $startOfWeek->format('Y-m-d'); // Untuk form filter, bisa juga $dateRef
                $actualEndDate = $endOfWeek->format('Y-m-d'); // Info tambahan
                $query->whereBetween('sale_date', [$startOfWeek, $endOfWeek]);
                $reportTitle = 'Laporan Mingguan (' . $startOfWeek->isoFormat('D MMM') . ' - ' . $endOfWeek->isoFormat('D MMM YYYY') . ')';
                break;

            case 'yearly':
                // Jika user memilih tanggal, tahun dihitung dari tanggal tsb
                // Jika tidak, tahun dihitung dari tanggal sekarang
                $year = $startDateInput ? Carbon::parse($startDateInput)->year : Carbon::now()->year;
                $actualStartDate = Carbon::createFromDate($year, 1, 1)->format('Y-m-d'); // Awal tahun untuk referensi form
                $query->whereYear('sale_date', $year);
                $reportTitle = 'Laporan Tahunan - ' . $year;
                break;

            case 'custom_range':
                if ($startDateInput && $endDateInput) {
                    $sDate = Carbon::parse($startDateInput)->startOfDay();
                    $eDate = Carbon::parse($endDateInput)->endOfDay();
                    $actualStartDate = $sDate->format('Y-m-d');
                    $actualEndDate = $eDate->format('Y-m-d');
                    $query->whereBetween('sale_date', [$sDate, $eDate]);
                    $reportTitle = 'Laporan Rentang (' . $sDate->isoFormat('D MMM YYYY') . ' - ' . $eDate->isoFormat('D MMM YYYY') . ')';
                } else {
                    // Jika custom range tapi tanggal tidak lengkap, fallback ke bulanan saat ini
                    $period = 'monthly'; // Ganti periode agar view filter konsisten
                    $date = Carbon::now();
                    $actualStartDate = $date->copy()->startOfMonth()->format('Y-m-d');
                    $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                    $reportTitle = 'Laporan Bulanan - ' . $date->isoFormat('MMMM YYYY');
                }
                break;

            case 'monthly':
            default: // Default ke bulanan
                $period = 'monthly'; // Pastikan $period diset
                // Jika user memilih tanggal, bulan dihitung dari tanggal tsb
                // Jika tidak, bulan dihitung dari tanggal sekarang
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $actualStartDate = $date->copy()->startOfMonth()->format('Y-m-d'); // Awal bulan untuk referensi form
                $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                $reportTitle = 'Laporan Bulanan - ' . $date->isoFormat('MMMM YYYY');
                break;
        }

        $sales = $query->orderBy('sale_date', 'asc')->get();

        $totalAmountSold = $sales->sum('total_amount_sold');
        $totalCostOfGoods = $sales->sum('total_cost_of_goods');
        $totalRevenue = $sales->sum('total_revenue');
        $numberOfSales = $sales->count();

        // Data untuk grafik: Revenue per hari dalam periode yang dipilih
        $chartData = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->sale_date)->format('Y-m-d'); // Grup berdasarkan tanggal
        })->mapWithKeys(function ($dailySales, $date) { // mapWithKeys untuk mempertahankan key tanggal
            return [$date => $dailySales->sum('total_revenue')];
        })->sortKeys(); // Urutkan berdasarkan tanggal untuk grafik yang benar

        return view('penjual.reports.revenue', compact(
            'sales',
            'reportTitle',
            'totalAmountSold',
            'totalCostOfGoods',
            'totalRevenue',
            'numberOfSales',
            'chartData',
            'period',        // Periode yang sedang aktif (string)
            'actualStartDate', // Untuk mengisi nilai default form start_date
            'actualEndDate'   // Untuk mengisi nilai default form end_date
        ));
    }

    /**
     * Handle export to Excel.
     */
    public function export(Request $request)
    {
        // Logika filter tanggal sama persis dengan method index()
        $period = $request->input('period', 'monthly');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $query = Sale::where('payment_status', 'Sudah Dibayar');
        $reportTitleForFile = 'Laporan Revenue'; // Judul default untuk nama file

        // (Salin blok switch-case dari method index() untuk menentukan query dan $reportTitleForFile)
        // Contoh singkat (Anda perlu melengkapi semua case):
        switch ($period) {
            case 'daily':
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::today();
                $query->whereDate('sale_date', $date);
                $reportTitleForFile = 'Harian_' . $date->format('Y-m-d');
                break;
            case 'weekly':
                $dateRef = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $startOfWeek = $dateRef->copy()->startOfWeek(Carbon::MONDAY);
                $endOfWeek = $dateRef->copy()->endOfWeek(Carbon::SUNDAY);
                $query->whereBetween('sale_date', [$startOfWeek, $endOfWeek]);
                $reportTitleForFile = 'Mingguan_' . $startOfWeek->format('Y-m-d') . '_sd_' . $endOfWeek->format('Y-m-d');
                break;
            // ... tambahkan case lainnya (monthly, yearly, custom_range) ...
            case 'monthly':
            default:
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                $reportTitleForFile = 'Bulanan_' . $date->format('Y-m');
                break;
        }

        $salesForExport = $query->orderBy('sale_date', 'asc')->get();

        // Ini adalah placeholder. Implementasi sebenarnya akan menggunakan Maatwebsite/Excel
        // $fileName = 'laporan_revenue_' . str_replace([' ', '(', ')', '-'], '_', strtolower($reportTitleForFile)) . '.xlsx';
        // return Excel::download(new RevenueReportExport($salesForExport), $fileName); // RevenueReportExport adalah class Exporter yg akan kita buat nanti

        // Untuk sekarang, kita kembalikan pesan saja
        $this->command->info("Data untuk export ($reportTitleForFile): " . $salesForExport->count() . " transaksi."); // Jika dijalankan dari seeder/command
        return response()->json([
            'message' => "Fitur export untuk '{$reportTitleForFile}' akan diimplementasikan menggunakan Maatwebsite/Excel.",
            'data_count' => $salesForExport->count()
        ]);
    }
}
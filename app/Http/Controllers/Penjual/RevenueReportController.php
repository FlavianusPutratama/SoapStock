<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\RevenueReportExport;   // <--- IMPORT CLASS EXPORT KITA
use Maatwebsite\Excel\Facades\Excel; // <--- IMPORT FACADE EXCEL

class RevenueReportController extends Controller
{
    // ... (method index() Anda yang sudah ada dan sudah mengambil data filter dengan benar) ...
    public function index(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $query = Sale::where('payment_status', 'Sudah Dibayar')
                     ->with(['user', 'items.productVariant.product']);

        $reportTitle = '';
        $actualStartDate = null;
        $actualEndDate = null;

        // (Logika switch-case untuk $period, $query, $reportTitle, $actualStartDate, $actualEndDate SAMA SEPERTI SEBELUMNYA)
        // ... BEGIN SALIN DARI METHOD INDEX ...
        switch ($period) {
            case 'daily':
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::today();
                $actualStartDate = $date->format('Y-m-d');
                $query->whereDate('sale_date', $date);
                $reportTitle = 'Laporan Harian - ' . $date->isoFormat('D MMMM YYYY');
                break;
            case 'weekly':
                $dateRef = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $startOfWeek = $dateRef->copy()->startOfWeek(Carbon::MONDAY);
                $endOfWeek = $dateRef->copy()->endOfWeek(Carbon::SUNDAY);
                $actualStartDate = $startOfWeek->format('Y-m-d');
                $actualEndDate = $endOfWeek->format('Y-m-d');
                $query->whereBetween('sale_date', [$startOfWeek, $endOfWeek]);
                $reportTitle = 'Laporan Mingguan (' . $startOfWeek->isoFormat('D MMM') . ' - ' . $endOfWeek->isoFormat('D MMM YYYY') . ')';
                break;
            case 'yearly':
                $year = $startDateInput ? Carbon::parse($startDateInput)->year : Carbon::now()->year;
                $actualStartDate = Carbon::createFromDate($year, 1, 1)->format('Y-m-d');
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
                    $period = 'monthly';
                    $date = Carbon::now();
                    $actualStartDate = $date->copy()->startOfMonth()->format('Y-m-d');
                    $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                    $reportTitle = 'Laporan Bulanan - ' . $date->isoFormat('MMMM YYYY');
                }
                break;
            case 'monthly':
            default:
                $period = 'monthly';
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $actualStartDate = $date->copy()->startOfMonth()->format('Y-m-d');
                $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                $reportTitle = 'Laporan Bulanan - ' . $date->isoFormat('MMMM YYYY');
                break;
        }
        // ... END SALIN DARI METHOD INDEX ...

        $sales = $query->orderBy('sale_date', 'asc')->get();

        $totalAmountSold = $sales->sum('total_amount_sold');
        $totalCostOfGoods = $sales->sum('total_cost_of_goods');
        $totalRevenue = $sales->sum('total_revenue');
        $numberOfSales = $sales->count();

        $chartData = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->sale_date)->format('Y-m-d');
        })->mapWithKeys(function ($dailySales, $date) {
            return [$date => $dailySales->sum('total_revenue')];
        })->sortKeys();

        return view('penjual.reports.revenue', compact(
            'sales', 'reportTitle', 'totalAmountSold', 'totalCostOfGoods',
            'totalRevenue', 'numberOfSales', 'chartData',
            'period', 'actualStartDate', 'actualEndDate'
        ));
    }


    public function export(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $query = Sale::where('payment_status', 'Sudah Dibayar')
                        ->with(['user', 'items.productVariant.product']); // Eager load penting untuk mapping

        $reportTitle = 'Laporan Revenue'; // Judul default untuk display di Excel
        $reportTitleForFile = 'semua_periode'; // Untuk nama file

        // SALIN Logika switch-case untuk $period, $query, $reportTitle, dan $reportTitleForFile
        // DARI method index() atau dari respons saya sebelumnya untuk export.
        // Ini penting agar data yang diexport sesuai dengan filter.
        // ... BEGIN SALIN BLOK SWITCH-CASE UNTUK FILTER ...
        switch ($period) {
            case 'daily':
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::today();
                $query->whereDate('sale_date', $date);
                $reportTitle = 'Laporan Harian - ' . $date->isoFormat('D MMMM YYYY');
                $reportTitleForFile = 'Harian_' . $date->format('Y-m-d');
                break;
            case 'weekly':
                $dateRef = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $startOfWeek = $dateRef->copy()->startOfWeek(Carbon::MONDAY);
                $endOfWeek = $dateRef->copy()->endOfWeek(Carbon::SUNDAY);
                $query->whereBetween('sale_date', [$startOfWeek, $endOfWeek]);
                $reportTitle = 'Laporan Mingguan (' . $startOfWeek->isoFormat('D MMM') . ' - ' . $endOfWeek->isoFormat('D MMM YYYY') . ')';
                $reportTitleForFile = 'Mingguan_' . $startOfWeek->format('Ymd') . '-' . $endOfWeek->format('Ymd');
                break;
            case 'yearly':
                $year = $startDateInput ? Carbon::parse($startDateInput)->year : Carbon::now()->year;
                $query->whereYear('sale_date', $year);
                $reportTitle = 'Laporan Tahunan - ' . $year;
                $reportTitleForFile = 'Tahunan_' . $year;
                break;
            case 'custom_range':
                if ($startDateInput && $endDateInput) {
                    $sDate = Carbon::parse($startDateInput)->startOfDay();
                    $eDate = Carbon::parse($endDateInput)->endOfDay();
                    $query->whereBetween('sale_date', [$sDate, $eDate]);
                    $reportTitle = 'Laporan Rentang (' . $sDate->isoFormat('D MMM YYYY') . ' - ' . $eDate->isoFormat('D MMM YYYY') . ')';
                    $reportTitleForFile = 'Custom_' . $sDate->format('Ymd') . '-' . $eDate->format('Ymd');
                } else {
                    $period = 'monthly'; // Fallback
                    $date = Carbon::now();
                    $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                    $reportTitle = 'Laporan Bulanan - ' . $date->isoFormat('MMMM YYYY');
                    $reportTitleForFile = 'Bulanan_' . $date->format('Y-m');
                }
                break;
            case 'monthly':
            default:
                $period = 'monthly';
                $date = $startDateInput ? Carbon::parse($startDateInput) : Carbon::now();
                $query->whereMonth('sale_date', $date->month)->whereYear('sale_date', $date->year);
                $reportTitle = 'Laporan Bulanan - ' . $date->isoFormat('MMMM YYYY');
                $reportTitleForFile = 'Bulanan_' . $date->format('Y-m');
                break;
        }
        // ... END SALIN BLOK SWITCH-CASE UNTUK FILTER ...

        $salesForExport = $query->orderBy('sale_date', 'asc')->get();
        
        // Siapkan data ringkasan untuk dikirim ke Export class
        $summary = [
            'totalAmountSold' => $salesForExport->sum('total_amount_sold'),
            'totalCostOfGoods' => $salesForExport->sum('total_cost_of_goods'),
            'totalRevenue' => $salesForExport->sum('total_revenue'),
            'numberOfSales' => $salesForExport->count(),
        ];

        $fileName = 'laporan_revenue_soapstock_' . str_replace([' ', '(', ')', '-', ','], '_', strtolower($reportTitleForFile)) . '_' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(new RevenueReportExport($salesForExport, $reportTitle, $period, $startDateInput, $endDateInput, $summary), $fileName);
    }
}
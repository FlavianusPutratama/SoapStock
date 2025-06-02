<?php

namespace App\Exports;

use App\Models\Sale; // Meskipun kita terima collection, baik untuk referensi tipe
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet; // Untuk styling atau event lain (opsional)
use Maatwebsite\Excel\Concerns\WithEvents;  // Untuk styling atau event lain (opsional)
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Untuk alignment
use Carbon\Carbon;

class RevenueReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $sales;
    protected $reportTitle;
    protected $period;
    protected $actualStartDate;
    protected $actualEndDate;
    protected $summary;


    public function __construct($sales, $reportTitle, $period, $actualStartDate, $actualEndDate, $summary)
    {
        $this->sales = $sales; // Ini adalah collection of Sale models yang sudah difilter
        $this->reportTitle = $reportTitle;
        $this->period = $period;
        $this->actualStartDate = $actualStartDate;
        $this->actualEndDate = $actualEndDate;
        $this->summary = $summary; // Array berisi totalAmountSold, totalCostOfGoods, dll.
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->sales;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Header untuk data utama
        return [
            'ID Order',
            'Tanggal Transaksi',
            'Nama Pelanggan',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Item Terjual (Nama - Ukuran x Jml)',
            'Total Omzet (Rp)',
            'Total Modal (Rp)',
            'Total Revenue (Rp)',
            'Diproses Oleh',
        ];
    }

    /**
    * @param mixed $sale  // Bisa di-type-hint ke Sale jika collection pasti berisi Sale
    * @return array
    */
    public function map($sale): array
    {
        $itemsString = $sale->items->map(function ($item) {
            $productName = $item->productVariant->product->name ?? 'Produk Dihapus';
            $variantSize = $item->productVariant->size ?? 'N/A';
            return "{$productName} ({$variantSize}) x{$item->quantity_sold}";
        })->implode("; \n"); // Gunakan newline untuk Excel jika banyak item

        return [
            'SO-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT),
            Carbon::parse($sale->sale_date)->isoFormat('DD MMM YYYY, HH:mm'),
            $sale->customer_name ?? '-',
            $sale->payment_method,
            $sale->payment_status,
            $itemsString,
            $sale->total_amount_sold,       // Format angka akan default, bisa diatur dengan WithColumnFormatting
            $sale->total_cost_of_goods,
            $sale->total_revenue,
            $sale->user->name ?? 'N/A',
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tambahkan Judul Laporan di baris pertama
                $event->sheet->insertNewRowBefore(1, 5); // Sisipkan 5 baris sebelum baris data pertama (header)
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->setCellValue('A1', 'LAPORAN REVENUE - ' . strtoupper(config('app.name', 'SoapStock')));
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->mergeCells('A2:J2');
                $event->sheet->setCellValue('A2', $this->reportTitle);
                $event->sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Tambahkan ringkasan setelah data
                $lastRow = $event->sheet->getHighestDataRow();
                $summaryStartRow = $lastRow + 2; // Beri jarak 1 baris kosong

                $event->sheet->setCellValue("A{$summaryStartRow}", 'RINGKASAN:');
                $event->sheet->getStyle("A{$summaryStartRow}")->getFont()->setBold(true);

                $event->sheet->setCellValue("A".($summaryStartRow+1), 'Total Omzet (Rp):');
                $event->sheet->setCellValue("B".($summaryStartRow+1), $this->summary['totalAmountSold']);
                $event->sheet->setCellValue("A".($summaryStartRow+2), 'Total Modal (HPP) (Rp):');
                $event->sheet->setCellValue("B".($summaryStartRow+2), $this->summary['totalCostOfGoods']);
                $event->sheet->setCellValue("A".($summaryStartRow+3), 'Total Keuntungan (Revenue) (Rp):');
                $event->sheet->setCellValue("B".($summaryStartRow+3), $this->summary['totalRevenue']);
                $event->sheet->getStyle("B".($summaryStartRow+1).":B".($summaryStartRow+3))->getNumberFormat()->setFormatCode('#,##0');


                $event->sheet->setCellValue("A".($summaryStartRow+4), 'Jumlah Transaksi:');
                $event->sheet->setCellValue("B".($summaryStartRow+4), $this->summary['numberOfSales']);


                // Styling Header Tabel Data (baris ke-5 setelah penambahan judul)
                $headerRow = 5;
                $event->sheet->getStyle("A{$headerRow}:J{$headerRow}")->getFont()->setBold(true);
                $event->sheet->getStyle("A{$headerRow}:J{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                 $event->sheet->getStyle("G5:I{$lastRow}")->getNumberFormat()->setFormatCode('#,##0'); // Format angka untuk kolom moneter
            },
        ];
    }
}
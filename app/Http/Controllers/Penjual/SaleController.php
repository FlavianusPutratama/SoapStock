<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Sale;
// use App\Models\SaleItem; // Tidak wajib di-import jika menggunakan relasi untuk createMany
use App\Http\Requests\StoreSaleRequest; // Pastikan path dan nama file ini benar
use Illuminate\Http\Request; // Hanya jika Anda menggunakan Request biasa di method lain
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon; // Jika Anda memanipulasi tanggal di sini, meskipun sekarang lebih banyak di FormRequest

class SaleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productVariants = ProductVariant::with('product')
                            ->where('current_stock', '>', 0) // Hanya tampilkan yang ada stok
                            ->orderBy('product_id')
                            ->get()
                            ->map(function ($variant) {
                                return [
                                    'id' => $variant->id,
                                    'name' => $variant->product->name . ' - ' . $variant->size,
                                    'stock' => $variant->current_stock,
                                    'price' => $variant->selling_price,
                                    'purchase_price' => $variant->purchase_price // Untuk COGS
                                ];
                            });
        return view('penjual.sales.create', compact('productVariants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            $totalAmountSold = 0;
            $totalCostOfGoods = 0;
            $saleItemsToCreate = [];

            foreach ($validatedData['items'] as $index => $itemData) {
                $variant = ProductVariant::findOrFail($itemData['product_variant_id']);

                if ($variant->current_stock < $itemData['quantity_sold']) {
                    DB::rollBack(); // Penting untuk rollback sebelum melempar exception
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity_sold" => 'Stok untuk produk ' . $variant->product->name . ' (' . $variant->size . ') hanya tersisa ' . $variant->current_stock . '. Kuantitas diminta: ' . $itemData['quantity_sold'] . '.',
                    ]);
                }

                $sellingPriceAtSale = $variant->selling_price;
                $purchasePriceAtSale = $variant->purchase_price;

                $totalAmountSold += $sellingPriceAtSale * $itemData['quantity_sold'];
                $totalCostOfGoods += $purchasePriceAtSale * $itemData['quantity_sold'];

                $variant->current_stock -= $itemData['quantity_sold'];
                $variant->updated_by_id = Auth::id();
                $variant->save();

                $saleItemsToCreate[] = [
                    'product_variant_id' => $variant->id,
                    'quantity_sold' => $itemData['quantity_sold'],
                    'selling_price_per_unit_at_sale' => $sellingPriceAtSale,
                    'purchase_price_per_unit_at_sale' => $purchasePriceAtSale,
                ];
            }

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_name' => $validatedData['customer_name'],
                'sale_date' => $validatedData['sale_date'], // Sudah diformat Y-m-d oleh StoreSaleRequest
                'payment_method' => $validatedData['payment_method'],
                'payment_status' => $validatedData['payment_status'],
                'total_amount_sold' => $totalAmountSold,
                'total_cost_of_goods' => $totalCostOfGoods,
                'total_revenue' => $totalAmountSold - $totalCostOfGoods,
                'notes' => $validatedData['notes'] ?? null,
            ]);

            if (!empty($saleItemsToCreate)) {
                $sale->items()->createMany($saleItemsToCreate);
            }

            DB::commit();

            // Ini bagian redirect dengan flash message
            return redirect()->route('penjual.dashboard') // Atau route lain yang Anda inginkan
                             ->with('success', 'Transaksi penjualan (ID: SO-'.str_pad($sale->id, 5, '0', STR_PAD_LEFT).') berhasil disimpan!');

        } catch (ValidationException $e) {
            // Tidak perlu DB::rollBack() di sini jika sudah ada sebelum throw $e
            throw $e; // Biarkan Laravel menangani redirect back with errors
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error saat menyimpan transaksi penjualan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString()); // Opsional: Log error
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Terjadi kesalahan sistem saat menyimpan transaksi. Silakan coba lagi atau hubungi administrator. Detail: ' . $e->getMessage()); // Tampilkan pesan error jika APP_DEBUG true
        }
    }

    // Tambahkan method lain jika ada (misalnya show, index untuk daftar penjualan penjual, dll.)
}
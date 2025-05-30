<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Http\Requests\StoreSaleRequest; // Sudah kita buat
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk Database Transaction
use Illuminate\Validation\ValidationException; // Untuk custom validation error

class SaleController extends Controller
{
    public function create()
    {
        $productVariants = ProductVariant::with('product')
                            ->where('current_stock', '>', 0) // Hanya tampilkan yang ada stok
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

    public function store(StoreSaleRequest $request)
    {
        $validatedData = $request->validated(); // Ini termasuk 'items' array

        DB::beginTransaction(); // Mulai transaksi

        try {
            $totalAmountSold = 0;
            $totalCostOfGoods = 0;

            // Kumpulkan detail item untuk disimpan nanti, setelah Sale header dibuat
            $saleItemsData = [];

            // Proses setiap item dan update stok
            foreach ($validatedData['items'] as $item) {
                $variant = ProductVariant::findOrFail($item['product_variant_id']);

                if ($variant->current_stock < $item['quantity_sold']) {
                    // Stok tidak cukup, batalkan transaksi
                    DB::rollBack();
                    // Kirim error kembali ke form, spesifik ke item yang bermasalah
                    // Ini cara manual membuat ValidationException
                    throw ValidationException::withMessages([
                        'items.' . array_search($item, $validatedData['items']) . '.quantity_sold' => 'Stok untuk ' . $variant->product->name . ' (' . $variant->size . ') tidak mencukupi. Sisa stok: ' . $variant->current_stock,
                    ]);
                }

                $sellingPriceAtSale = $variant->selling_price; // Ambil harga jual saat ini dari varian
                $purchasePriceAtSale = $variant->purchase_price; // Ambil harga pokok saat ini dari varian

                $totalAmountSold += $sellingPriceAtSale * $item['quantity_sold'];
                $totalCostOfGoods += $purchasePriceAtSale * $item['quantity_sold'];

                // Kurangi stok
                $variant->current_stock -= $item['quantity_sold'];
                $variant->updated_by_id = Auth::id();
                $variant->save();

                $saleItemsData[] = [
                    'product_variant_id' => $variant->id,
                    'quantity_sold' => $item['quantity_sold'],
                    'selling_price_per_unit_at_sale' => $sellingPriceAtSale,
                    'purchase_price_per_unit_at_sale' => $purchasePriceAtSale,
                ];
            }

            // Buat record Sale (header)
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_name' => $validatedData['customer_name'],
                'sale_date' => $validatedData['sale_date'],
                'payment_method' => $validatedData['payment_method'],
                'payment_status' => $validatedData['payment_status'],
                'total_amount_sold' => $totalAmountSold,
                'total_cost_of_goods' => $totalCostOfGoods,
                'total_revenue' => $totalAmountSold - $totalCostOfGoods,
                'notes' => $validatedData['notes'],
            ]);

            // Buat record SaleItem untuk setiap item
            foreach ($saleItemsData as $itemData) {
                $sale->items()->create($itemData); // Menggunakan relasi untuk membuat SaleItem
            }

            DB::commit(); // Semua berhasil, commit transaksi

            // return redirect()->route('penjual.dashboard')->with('success', 'Transaksi penjualan berhasil disimpan!'); // atau ke detail penjualan
            return "Transaksi Penjualan BERHASIL disimpan. ID Sale: {$sale->id}. Redirecting...";

        } catch (ValidationException $e) {
            DB::rollBack(); // Rollback jika ada ValidationException dari cek stok
            throw $e; // Lempar kembali ValidationException agar Laravel menanganinya (kembali ke form dengan error)
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error lain
            // return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
            return "ERROR: Gagal menyimpan transaksi: " . $e->getMessage();
        }
    }
}
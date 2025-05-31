<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockIn;
use App\Http\Requests\StoreStockInRequest;
use Illuminate\Http\Request; // Tidak terpakai jika hanya ada create & store dengan FormRequest
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productVariants = ProductVariant::with('product')
                            ->get()
                            ->map(function ($variant) {
                                return [
                                    'id' => $variant->id,
                                    'name' => $variant->product->name . ' - ' . $variant->size . ' (Stok: ' . $variant->current_stock . ')'
                                ];
                            })->sortBy('name');
        return view('penjual.stockins.create', compact('productVariants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockInRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id(); // User yang melakukan input

        try {
            DB::transaction(function () use ($validatedData) {
                $variant = ProductVariant::findOrFail($validatedData['product_variant_id']);
                $actualPurchasePriceForBatch = null;

                // Tentukan harga beli aktual untuk batch ini DAN update harga master jika diinput baru
                if (isset($validatedData['purchase_price_at_entry']) && !is_null($validatedData['purchase_price_at_entry'])) {
                    // Jika harga beli diinput di form, gunakan itu untuk batch ini
                    // DAN update harga beli master di ProductVariant
                    $actualPurchasePriceForBatch = $validatedData['purchase_price_at_entry'];
                    $variant->purchase_price = $actualPurchasePriceForBatch; // Update harga master
                } else {
                    // Jika harga beli tidak diinput di form (dibiarkan kosong),
                    // harga beli master di ProductVariant TIDAK BERUBAH.
                    // Gunakan harga beli master yang sudah ada di ProductVariant untuk batch ini.
                    $actualPurchasePriceForBatch = $variant->purchase_price;
                }

                // Buat entri StockIn dengan harga beli aktual untuk batch ini
                StockIn::create([
                    'product_variant_id' => $validatedData['product_variant_id'],
                    'user_id' => $validatedData['user_id'],
                    'quantity_added' => $validatedData['quantity_added'],
                    'purchase_price_at_entry' => $actualPurchasePriceForBatch, // Simpan harga aktual batch
                    'selling_price_set_at_entry' => $validatedData['selling_price_set_at_entry'] ?? null,
                    'entry_date' => $validatedData['entry_date'],
                    'supplier_name' => $validatedData['supplier_name'] ?? null,
                    'notes' => $validatedData['notes'] ?? null,
                ]);

                // Update stok di ProductVariant
                $variant->current_stock += $validatedData['quantity_added'];

                // Update harga jual di varian jika diinput baru
                if (isset($validatedData['selling_price_set_at_entry']) && !is_null($validatedData['selling_price_set_at_entry'])) {
                    $variant->selling_price = $validatedData['selling_price_set_at_entry'];
                }

                $variant->updated_by_id = Auth::id(); // Catat siapa yang terakhir update varian
                $variant->save(); // Simpan semua perubahan pada ProductVariant
            });

            // Jika transaksi berhasil
            $redirectRoute = auth()->user()->role == 'penjual' ? 'penjual.dashboard' : 'inventory.products.index';
            return redirect()->route($redirectRoute)
                             ->with('success', 'Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika terjadi error selama transaksi, redirect kembali dengan pesan error
            // withInput() akan membawa kembali data input sebelumnya ke form
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan stok masuk: ' . $e->getMessage());
        }
    }
}
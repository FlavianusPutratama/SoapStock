<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockIn;
use App\Http\Requests\StoreStockInRequest; // Pastikan ini sudah dibuat
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk Database Transaction

class StockInController extends Controller
{
    // Method create() sudah ada dari respons sebelumnya...
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
                // 1. Buat entri baru di tabel stock_ins (catatan histori stok masuk)
                StockIn::create([
                    'product_variant_id' => $validatedData['product_variant_id'],
                    'user_id' => $validatedData['user_id'],
                    'quantity_added' => $validatedData['quantity_added'],
                    'purchase_price_at_entry' => $validatedData['purchase_price_at_entry'],
                    'selling_price_set_at_entry' => $validatedData['selling_price_set_at_entry'] ?? null,
                    'entry_date' => $validatedData['entry_date'],
                    'supplier_name' => $validatedData['supplier_name'] ?? null,
                    'notes' => $validatedData['notes'] ?? null,
                ]);

                // 2. Update data di tabel product_variants
                $variant = ProductVariant::findOrFail($validatedData['product_variant_id']);

                // Tambah stok saat ini
                $variant->current_stock += $validatedData['quantity_added'];

                // Update harga beli varian dengan harga beli terakhir saat entry
                $variant->purchase_price = $validatedData['purchase_price_at_entry'];

                // Jika harga jual baru di-set saat entry, update harga jual varian
                if (isset($validatedData['selling_price_set_at_entry']) && !is_null($validatedData['selling_price_set_at_entry'])) {
                    $variant->selling_price = $validatedData['selling_price_set_at_entry'];
                }

                // Catat siapa yang terakhir update varian ini
                $variant->updated_by_id = Auth::id();

                $variant->save();
            });

            // Jika transaksi berhasil
            return redirect()->route(auth()->user()->role == 'penjual' ? 'penjual.dashboard' : 'inventory.products.index')
                             ->with('success', 'Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika terjadi error selama transaksi, redirect kembali dengan pesan error
            // withInput() akan membawa kembali data input sebelumnya ke form
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan stok masuk: ' . $e->getMessage());
        }
    }
}
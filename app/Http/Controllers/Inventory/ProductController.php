<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant; // Tambahkan ini
use App\Models\StockIn;       // Tambahkan ini
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest; // Untuk method update
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;     // Tambahkan ini untuk transaction
use Illuminate\Database\QueryException; // Untuk menangkap error database
use Illuminate\Http\Request;          // Tidak terpakai jika semua action pakai FormRequest

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('creator') // Tetap eager load creator
                            ->withCount('variants') // Tetap hitung jumlah varian
                            ->withSum('variants', 'current_stock') // TAMBAHKAN INI untuk menjumlahkan stok varian
                            ->orderBy('name', 'asc')
                            ->paginate(10);
        return view('inventory.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pastikan view ini akan dimodifikasi untuk menangani input varian
        return view('inventory.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        // Ambil data produk utama
        $productData = $request->only(['name', 'description']);
        // Ambil data varian, default array kosong jika tidak ada
        $variantsInput = $request->input('variants', []);

        DB::beginTransaction();

        try {
            // 1. Simpan Produk Utama
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'] ?? null,
                'created_by_id' => Auth::id(),
                'updated_by_id' => Auth::id(),
            ]);

            // 2. Jika ada data varian, simpan setiap varian dan buat StockIn untuk stok awalnya
            if (!empty($variantsInput)) {
                foreach ($variantsInput as $variantInputData) {
                    // Skip baris varian yang mungkin tidak sengaja terkirim kosong oleh JS (jika size kosong)
                    if (empty($variantInputData['size'])) {
                        continue;
                    }

                    // Buat ProductVariant
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variantInputData['size'],
                        'current_stock' => $variantInputData['current_stock'] ?? 0,
                        'purchase_price' => $variantInputData['purchase_price'],
                        'selling_price' => $variantInputData['selling_price'],
                        'created_by_id' => Auth::id(),
                        'updated_by_id' => Auth::id(),
                    ]);

                    // Jika stok awal lebih dari 0, buat record StockIn
                    if ($variant->current_stock > 0) {
                        StockIn::create([
                            'product_variant_id' => $variant->id,
                            'user_id' => Auth::id(),
                            'quantity_added' => $variant->current_stock,
                            'purchase_price_at_entry' => $variant->purchase_price,
                            // selling_price_set_at_entry bisa null, tidak perlu diisi di sini
                            'entry_date' => now()->toDateString(),
                            'supplier_name' => 'Stok Awal (Produk Baru)', // Catatan untuk stok awal
                            'notes' => 'Stok awal otomatis saat pembuatan produk dan varian baru.',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('inventory.products.show', $product->id)
                             ->with('success', 'Produk "' . $product->name . '" beserta variannya (jika ada) berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error saat menyimpan produk baru beserta varian: ' . $e->getMessage()); // Opsional: log error
            return redirect()->back()
                             ->withInput() // Mengembalikan input sebelumnya ke form
                             ->with('error', 'Gagal menyimpan produk. Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load([
            'creator',
            'updater',
            'variants' => function ($query) {
                $query->with(['creator', 'updater'])->orderBy('size', 'asc');
            }
        ]);
        return view('inventory.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('inventory.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();
        $validatedData['updated_by_id'] = Auth::id();

        $product->update($validatedData);

        return redirect()->route('inventory.products.index')
                         ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        try {
            if ($product->variants()->exists()) {
                return redirect()->route('inventory.products.index')
                                 ->with('error', "Produk '{$productName}' tidak dapat dihapus karena masih memiliki varian terkait.");
            }
            $product->delete();
            return redirect()->route('inventory.products.index')
                             ->with('success', "Produk '{$productName}' berhasil dihapus!");
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('inventory.products.index')
                                 ->with('error', "Produk '{$productName}' tidak dapat dihapus karena masih terikat dengan data lain.");
            }
            return redirect()->route('inventory.products.index')
                             ->with('error', "Terjadi kesalahan database saat menghapus produk '{$productName}'.");
        }
    }
}
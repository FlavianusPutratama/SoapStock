<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use Illuminate\Support\Facades\Auth;

class ProductVariantController extends Controller
{
    // Menampilkan form tambah varian untuk produk tertentu
    public function create(Product $product) // $product diambil dari route /products/{product}/variants/create
    {
        return view('inventory.variants.create', compact('product'));
    }

    // Menyimpan varian baru untuk produk tertentu
    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $validatedData = $request->validated();
        $validatedData['product_id'] = $product->id; // Set product_id dari parameter route
        $validatedData['created_by_id'] = Auth::id();
        $validatedData['updated_by_id'] = Auth::id();

        ProductVariant::create($validatedData);

        // return redirect()->route('inventory.products.show', $product)->with('success', 'Varian produk berhasil ditambahkan!');
        // atau redirect ke halaman detail produk, atau daftar varian produk tsb
        return "Varian '{$validatedData['size']}' untuk Produk '{$product->name}' BERHASIL disimpan. Redirecting...";
    }

    // Menampilkan form edit untuk varian tertentu
    public function edit(ProductVariant $variant) // $variant diambil dari route /variants/{variant}/edit
    {
        $product = $variant->product; // Untuk mendapatkan info produk induknya
        return view('inventory.variants.edit', compact('variant', 'product'));
    }

    // Mengupdate varian tertentu
    public function update(UpdateProductVariantRequest $request, ProductVariant $variant)
    {
        $validatedData = $request->validated();
        $validatedData['updated_by_id'] = Auth::id();

        $variant->update($validatedData);

        // return redirect()->route('inventory.products.show', $variant->product_id)->with('success', 'Varian produk berhasil diperbarui!');
        return "Varian '{$variant->size}' (Produk: {$variant->product->name}) BERHASIL diupdate. Redirecting...";
    }

    // Menghapus varian tertentu
    public function destroy(ProductVariant $variant)
    {
        // Cek apakah varian terkait dengan transaksi (stock_ins atau sale_items)
        if ($variant->stockIns()->exists() || $variant->saleItems()->exists()) {
            // return redirect()->back()->with('error', "Varian '{$variant->size}' tidak dapat dihapus karena memiliki histori transaksi.");
             return "ERROR: Varian '{$variant->size}' tidak dapat dihapus karena memiliki histori transaksi.";
        }

        $variantSize = $variant->size;
        $productName = $variant->product->name;
        $productId = $variant->product_id;
        $variant->delete();

        // return redirect()->route('inventory.products.show', $productId)->with('success', "Varian '{$variantSize}' berhasil dihapus dari produk '{$productName}'.");
        return "Varian '{$variantSize}' (Produk: {$productName}) BERHASIL dihapus. Redirecting...";
    }
}
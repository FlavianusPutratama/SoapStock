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

        // Redirect ke halaman detail produk dengan pesan sukses
        return redirect()->route('inventory.products.show', $product->id) // $product->id agar lebih eksplisit
                         ->with('success', 'Varian produk "' . $validatedData['size'] . '" berhasil ditambahkan untuk produk "' . $product->name . '".');
        // Baris return di bawah ini sudah tidak terjangkau dan bisa dihapus:
        // return "Varian '{$validatedData['size']}' untuk Produk '{$product->name}' BERHASIL disimpan. Redirecting...";
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
        $product = $variant->product; // Ambil produk induknya untuk redirect

        return redirect()->route('inventory.products.show', $product->id)
                         ->with('success', 'Varian produk "' . $variant->size . '" berhasil diperbarui.');
    }

    // Menghapus (Soft Delete) varian tertentu
    public function destroy(ProductVariant $variant)
    {
        $variantSize = $variant->size;
        $product = $variant->product; // Simpan produk induk untuk redirect

        // Dengan SoftDeletes trait di model ProductVariant, panggilan delete() akan melakukan soft delete.
        // Pengecekan $variant->stockIns()->exists() || $variant->saleItems()->exists() tidak lagi
        // diperlukan untuk MENCEGAH delete, karena soft delete aman untuk integritas data historis.
        // Jika Anda tetap ingin ada pesan berbeda jika ada histori, pengecekan bisa saja dipertahankan
        // hanya untuk tujuan informasi, tapi untuk fungsionalitas dasar soft delete, ini sudah cukup.

        $variant->delete(); // Ini akan melakukan SOFT DELETE (mengisi kolom deleted_at)

        // Pesan bisa disesuaikan untuk lebih mencerminkan bahwa ini adalah penonaktifan/soft delete
        return redirect()->route('inventory.products.show', $product->id)
                         ->with('success', "Varian '{$variantSize}' berhasil dihapus (dinonaktifkan) dari produk '{$product->name}'.");
    }
}
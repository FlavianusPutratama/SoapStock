<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;    // Pastikan sudah dibuat
use App\Http\Requests\UpdateProductRequest;    // Pastikan sudah dibuat
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;      // Untuk menangkap error database
use Illuminate\Http\Request;                 // Bisa digunakan jika tidak ada Form Request spesifik

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load 'creator' untuk menampilkan nama pembuat
        // withCount('variants') untuk menghitung jumlah varian tanpa N+1 query
        $products = Product::with('creator')
                            ->withCount('variants')
                            ->orderBy('name', 'asc')
                            ->paginate(10); // Ganti angka 10 sesuai kebutuhan paginasi Anda

        return view('inventory.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by_id'] = Auth::id();
        $validatedData['updated_by_id'] = Auth::id(); // Saat dibuat, updated_by juga diisi

        Product::create($validatedData);

        return redirect()->route('inventory.products.index')
                         ->with('success', 'Produk "' . $validatedData['name'] . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Eager load relasi yang dibutuhkan untuk halaman detail
        // Termasuk creator dan updater dari produk itu sendiri, dan varian beserta creator/updater varian
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
            // Migration kita sudah set onDelete('restrict') untuk product_variants.product_id
            // Jadi, jika ada varian, database akan mencegah penghapusan produk.
            // Kita bisa cek dulu untuk memberikan pesan yang lebih ramah.
            if ($product->variants()->exists()) { // Cek apakah ada varian terkait
                return redirect()->route('inventory.products.index')
                                 ->with('error', "Produk '{$productName}' tidak dapat dihapus karena masih memiliki varian terkait.");
            }

            // Jika tidak ada varian, lanjutkan penghapusan
            $product->delete();

            return redirect()->route('inventory.products.index')
                             ->with('success', "Produk '{$productName}' berhasil dihapus!");

        } catch (QueryException $e) {
            // Menangkap error jika ada constraint lain atau masalah database
            // Kode '23000' adalah kode umum untuk integrity constraint violation di SQL
            if ($e->getCode() == "23000") {
                return redirect()->route('inventory.products.index')
                                 ->with('error', "Produk '{$productName}' tidak dapat dihapus karena masih terikat dengan data lain di sistem.");
            }
            // Error umum lainnya
            return redirect()->route('inventory.products.index')
                             ->with('error', "Terjadi kesalahan saat mencoba menghapus produk '{$productName}'. Pesan: " . $e->getMessage());
        }
    }
}
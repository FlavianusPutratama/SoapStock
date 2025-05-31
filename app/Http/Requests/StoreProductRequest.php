<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Jika diperlukan untuk aturan kompleks

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Di-handle oleh middleware role di route
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'description' => ['nullable', 'string', 'max:5000'],

            // Validasi untuk array varian
            'variants' => ['nullable', 'array'], // Produk boleh dibuat tanpa varian awal
            'variants.*.size' => ['required_with:variants', 'string', 'max:100'], // Wajib jika array 'variants' ada dan field size diisi
            'variants.*.current_stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.purchase_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.selling_price' => ['required_with:variants', 'numeric', 'min:0'],
            // Catatan: Uniqueness untuk 'variants.*.size' per produk baru ini
            // akan ditangani oleh unique constraint di database (product_id, size)
            // dan DB transaction akan me-rollback jika ada duplikat.
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.unique' => 'Nama produk ini sudah ada.',
            'description.max' => 'Deskripsi produk terlalu panjang.',

            // Pesan untuk validasi varian
            'variants.array' => 'Data varian harus berupa array.',
            'variants.*.size.required_with' => 'Ukuran untuk varian ke-:position wajib diisi jika varian ditambahkan.',
            'variants.*.size.string' => 'Ukuran untuk varian ke-:position harus berupa teks.',
            'variants.*.size.max' => 'Ukuran untuk varian ke-:position terlalu panjang (maks 100 karakter).',

            'variants.*.current_stock.required_with' => 'Stok awal untuk varian ke-:position wajib diisi.',
            'variants.*.current_stock.integer' => 'Stok awal untuk varian ke-:position harus berupa angka bulat.',
            'variants.*.current_stock.min' => 'Stok awal untuk varian ke-:position minimal 0.',

            'variants.*.purchase_price.required_with' => 'Harga beli untuk varian ke-:position wajib diisi.',
            'variants.*.purchase_price.numeric' => 'Harga beli untuk varian ke-:position harus berupa angka.',
            'variants.*.purchase_price.min' => 'Harga beli untuk varian ke-:position tidak boleh negatif.',

            'variants.*.selling_price.required_with' => 'Harga jual untuk varian ke-:position wajib diisi.',
            'variants.*.selling_price.numeric' => 'Harga jual untuk varian ke-:position harus berupa angka.',
            'variants.*.selling_price.min' => 'Harga jual untuk varian ke-:position tidak boleh negatif.',
        ];
    }
}
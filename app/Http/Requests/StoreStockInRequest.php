<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Asumsikan otorisasi sudah dihandle oleh middleware pada route
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
            'product_variant_id' => ['required', 'integer', Rule::exists('product_variants', 'id')],
            'quantity_added' => ['required', 'integer', 'min:1'],
            // Harga beli di form sekarang opsional untuk tujuan update master, tapi controller akan handle nilainya
            'purchase_price_at_entry' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'], // Tambahkan max jika perlu
            'selling_price_set_at_entry' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'entry_date' => ['required', 'date'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * (Opsional)
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'product_variant_id.required' => 'Varian produk wajib dipilih.',
            'product_variant_id.exists' => 'Varian produk yang dipilih tidak valid.',
            'quantity_added.required' => 'Jumlah stok masuk wajib diisi.',
            'quantity_added.integer' => 'Jumlah stok masuk harus berupa angka.',
            'quantity_added.min' => 'Jumlah stok masuk minimal 1.',
            'purchase_price_at_entry.numeric' => 'Harga beli baru harus berupa angka.',
            'purchase_price_at_entry.min' => 'Harga beli baru tidak boleh negatif.',
            'selling_price_set_at_entry.numeric' => 'Harga jual baru harus berupa angka.',
            'selling_price_set_at_entry.min' => 'Harga jual baru tidak boleh negatif.',
            'entry_date.required' => 'Tanggal barang masuk wajib diisi.',
            'entry_date.date' => 'Format tanggal barang masuk tidak valid.',
        ];
    }
}
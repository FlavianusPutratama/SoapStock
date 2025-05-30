<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Akan dihandle oleh middleware role di route
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
            'purchase_price_at_entry' => ['required', 'numeric', 'min:0'],
            'selling_price_set_at_entry' => ['nullable', 'numeric', 'min:0'], // Bisa jadi mengambil dari harga jual varian yg sudah ada
            'entry_date' => ['required', 'date'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_variant_id.required' => 'Varian produk wajib dipilih.',
            'product_variant_id.exists' => 'Varian produk yang dipilih tidak valid.',
            'quantity_added.required' => 'Jumlah stok masuk wajib diisi.',
            'quantity_added.integer' => 'Jumlah stok masuk harus berupa angka.',
            'quantity_added.min' => 'Jumlah stok masuk minimal 1.',
            'purchase_price_at_entry.required' => 'Harga beli wajib diisi.',
            'purchase_price_at_entry.numeric' => 'Harga beli harus berupa angka.',
            'entry_date.required' => 'Tanggal barang masuk wajib diisi.',
            'entry_date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
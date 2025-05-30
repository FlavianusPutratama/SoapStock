<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Di-handle middleware role
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:255'],
            'sale_date' => ['required', 'date_format:Y-m-d H:i:s'], // Atau hanya 'date' jika tidak perlu jam & menit
            'payment_method' => ['required', 'string', Rule::in(['Cash', 'Transfer', 'QRIS', 'Lainnya'])], // Sesuaikan metodenya
            'payment_status' => ['required', 'string', Rule::in(['Belum Dibayar', 'Sudah Dibayar'])], // Sesuaikan statusnya
            'notes' => ['nullable', 'string', 'max:5000'],

            // Validasi untuk item-item penjualan (array of items)
            'items' => ['required', 'array', 'min:1'], // Minimal harus ada 1 item
            'items.*.product_variant_id' => ['required', 'integer', Rule::exists('product_variants', 'id')],
            'items.*.quantity_sold' => ['required', 'integer', 'min:1'],
            // 'items.*.selling_price_per_unit_at_sale' => ['required', 'numeric', 'min:0'], // Harga jual bisa diambil dari DB saat proses
        ];
    }

    public function messages(): array
    {
        return [
            'sale_date.required' => 'Tanggal penjualan wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_status.required' => 'Status pembayaran wajib dipilih.',

            'items.required' => 'Minimal harus ada satu item produk dalam penjualan.',
            'items.min' => 'Minimal harus ada satu item produk dalam penjualan.',
            'items.*.product_variant_id.required' => 'Produk untuk item ke-:position wajib dipilih.',
            'items.*.product_variant_id.exists' => 'Produk untuk item ke-:position tidak valid.',
            'items.*.quantity_sold.required' => 'Jumlah untuk item ke-:position wajib diisi.',
            'items.*.quantity_sold.min' => 'Jumlah untuk item ke-:position minimal 1.',
        ];
    }
}
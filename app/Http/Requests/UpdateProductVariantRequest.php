<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        // $this->route('variant') akan berisi objek ProductVariant
        $variantId = $this->route('variant')->id ?? null;
        $productId = $this->route('variant')->product_id ?? null; // Ambil product_id dari varian yang diupdate

        return [
            'size' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_variants')->where(function ($query) use ($productId) {
                    return $query->where('product_id', $productId);
                })->ignore($variantId), // Abaikan varian saat ini
            ],
            'current_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
        ];
    }
    public function messages(): array { /* Tambahkan pesan kustom */
         return [
            'size.required' => 'Ukuran varian wajib diisi.',
            'size.unique' => 'Ukuran ini sudah ada untuk produk yang dipilih.',
            // ... pesan lainnya
        ];
    }
}
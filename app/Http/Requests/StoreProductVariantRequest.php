<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        // $this->route('product') akan berisi objek Product dari route /products/{product}/variants/...
        $productId = $this->route('product')->id ?? null;

        return [
            'size' => [
                'required',
                'string',
                'max:100',
                // Pastikan size unik untuk product_id ini
                Rule::unique('product_variants')->where(function ($query) use ($productId) {
                    return $query->where('product_id', $productId);
                }),
            ],
            'current_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
        ];
    }
    public function messages(): array { /* Tambahkan pesan kustom jika perlu */
        return [
            'size.required' => 'Ukuran varian wajib diisi.',
            'size.unique' => 'Ukuran ini sudah ada untuk produk yang dipilih.',
            // ... pesan lainnya
        ];
    }
}
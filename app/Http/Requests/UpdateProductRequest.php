<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Jangan lupa import Rule

class UpdateProductRequest extends FormRequest
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
        // $this->product akan berisi ID produk dari route model binding
        // atau $this->route('product') akan berisi objek Product jika menggunakan route model binding
        $productId = $this->route('product')->id ?? null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId), // Abaikan produk saat ini saat cek unique
            ],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.unique' => 'Nama produk ini sudah ada.',
            'description.max' => 'Deskripsi produk terlalu panjang.',
        ];
    }
}
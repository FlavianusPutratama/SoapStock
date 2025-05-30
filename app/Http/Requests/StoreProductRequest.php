<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Kita akan mengandalkan middleware 'role:superadmin,penjual' pada route.
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
            'name' => ['required', 'string', 'max:255', 'unique:products,name'], // Nama produk harus unik
            'description' => ['nullable', 'string', 'max:5000'],
            // 'created_by_id' dan 'updated_by_id' akan diisi secara otomatis di controller
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
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Kita akan mengandalkan middleware 'role:superadmin' pada route untuk otorisasi ini.
     * Jadi, di sini kita bisa return true.
     * Jika ada logika otorisasi yang lebih spesifik terkait data, bisa ditambahkan di sini.
     */
    public function authorize(): bool
    {
        // Contoh: Pastikan hanya user dengan role superadmin yang bisa membuat user baru
        // return $this->user()->role === 'superadmin';
        // Untuk saat ini, karena kita akan pasang middleware di route, kita set true
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')], // Pastikan email unik di tabel users
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' akan mencocokkan dengan field 'password_confirmation'
            'role' => ['required', 'string', Rule::in(['superadmin', 'penjual'])], // Pastikan role yang diinput valid
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
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Peran pengguna yang dipilih tidak valid.',
        ];
    }
}
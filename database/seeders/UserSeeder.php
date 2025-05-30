<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Import model User
use Illuminate\Support\Facades\Hash; // Import Hash facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@soapstock.app',
            'password' => Hash::make('password'), // Ganti dengan password yang aman!
            'role' => 'superadmin',
            'email_verified_at' => now(), // Langsung set terverifikasi
        ]);

        // Membuat Penjual Contoh 1
        User::create([
            'name' => 'Penjual Satu',
            'email' => 'penjual1@soapstock.app',
            'password' => Hash::make('password'), // Ganti dengan password yang aman!
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        // Membuat Penjual Contoh 2
        User::create([
            'name' => 'Penjual Dua',
            'email' => 'penjual2@soapstock.app',
            'password' => Hash::make('password'), // Ganti dengan password yang aman!
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        // Anda bisa menambahkan user lain jika perlu
        // User::factory(5)->create(); // Jika Anda ingin membuat user dummy tambahan dengan factory
    }
}
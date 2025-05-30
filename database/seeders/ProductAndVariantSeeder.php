<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;        // Import model Product
use App\Models\ProductVariant; // Import model ProductVariant
use App\Models\User;           // Import model User

class ProductAndVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user Super Admin (asumsikan ID 1 atau email tertentu)
        // Cara lebih aman adalah mencari berdasarkan email atau role
        $superAdmin = User::where('role', 'superadmin')->first();

        if (!$superAdmin) {
            $this->command->error('Super Admin user not found. Please run UserSeeder first or ensure a superadmin exists.');
            return;
        }

        // Produk 1: Sabun Mandi Batang
        $product1 = Product::create([
            'name' => 'Sabun Mandi Batang LifeWellness',
            'description' => 'Sabun mandi antiseptik untuk keluarga.',
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);

        ProductVariant::create([
            'product_id' => $product1->id,
            'size' => '100g',
            'current_stock' => 50,
            'purchase_price' => 2000,
            'selling_price' => 3500,
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);

        ProductVariant::create([
            'product_id' => $product1->id,
            'size' => 'Paket Isi 3 (3x100g)',
            'current_stock' => 20,
            'purchase_price' => 5500,
            'selling_price' => 9000,
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);

        // Produk 2: Sabun Cuci Piring Cair
        $product2 = Product::create([
            'name' => 'Sabun Cuci Piring SparkleClean',
            'description' => 'Efektif mengangkat lemak membandel.',
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);

        ProductVariant::create([
            'product_id' => $product2->id,
            'size' => '450ml Pouch',
            'current_stock' => 30,
            'purchase_price' => 8000,
            'selling_price' => 12000,
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);

        ProductVariant::create([
            'product_id' => $product2->id,
            'size' => '750ml Botol',
            'current_stock' => 15,
            'purchase_price' => 12000,
            'selling_price' => 18000,
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);
         // Produk 3: Sabun Colek
         $product3 = Product::create([
            'name' => 'Sabun Colek Super Bersih',
            'description' => 'Membersihkan noda pada pakaian dan perabotan.',
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);

        ProductVariant::create([
            'product_id' => $product3->id,
            'size' => '500g Ember',
            'current_stock' => 25,
            'purchase_price' => 7000,
            'selling_price' => 10000,
            'created_by_id' => $superAdmin->id,
            'updated_by_id' => $superAdmin->id,
        ]);
    }
}
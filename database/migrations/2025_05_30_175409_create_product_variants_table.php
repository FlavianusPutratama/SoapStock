<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict'); // restrict deletion of product if variants exist
            $table->string('size'); // Misal: "100ml", "250g"
            $table->integer('current_stock')->default(0);
            $table->decimal('purchase_price', 10, 2); // Harga beli (modal)
            $table->decimal('selling_price', 10, 2);  // Harga jual
            // $table->integer('low_stock_threshold')->nullable(); // Opsional
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Menambahkan unique constraint untuk kombinasi product_id dan size
            $table->unique(['product_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
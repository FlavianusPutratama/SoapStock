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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade'); // Jika sale dihapus, itemnya ikut terhapus
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('restrict'); // Jangan hapus variant jika pernah terjual
            $table->integer('quantity_sold');
            $table->decimal('selling_price_per_unit_at_sale', 10, 2);
            $table->decimal('purchase_price_per_unit_at_sale', 10, 2); // Modal per unit saat itu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
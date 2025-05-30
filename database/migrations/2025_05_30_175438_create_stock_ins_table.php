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
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User yang melakukan entry
            $table->integer('quantity_added');
            $table->decimal('purchase_price_at_entry', 10, 2);
            $table->decimal('selling_price_set_at_entry', 10, 2)->nullable(); // Harga jual bisa diset saat ini, atau dari variant
            $table->date('entry_date');
            $table->string('supplier_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
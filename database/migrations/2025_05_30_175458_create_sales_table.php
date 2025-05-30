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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Penjual
            $table->string('customer_name')->nullable(); // Bisa jadi pembeli tidak mau disebutkan namanya
            $table->dateTime('sale_date');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('Belum Dibayar'); // "Belum Dibayar", "Sudah Dibayar", "Dibatalkan"
            $table->decimal('total_amount_sold', 15, 2)->default(0); // Total harga jual
            $table->decimal('total_cost_of_goods', 15, 2)->default(0); // Total harga pokok/modal
            $table->decimal('total_revenue', 15, 2)->default(0); // total_amount_sold - total_cost_of_goods
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
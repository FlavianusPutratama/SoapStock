<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_variant_id',
        'quantity_sold',
        'selling_price_per_unit_at_sale', // Harga jual per unit pada saat transaksi
        'purchase_price_per_unit_at_sale',// Harga beli (modal) per unit pada saat transaksi (untuk hitung revenue)
    ];

    /**
     * Get the sale that this item belongs to.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product variant that was sold.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'user_id',                      // ID user yang melakukan entry stok masuk
        'quantity_added',
        'purchase_price_at_entry',    // Harga beli per unit pada saat entry ini
        'selling_price_set_at_entry', // Harga jual yang di-set pada saat entry ini (bisa mengupdate ProductVariant)
        'entry_date',                 // Tanggal barang masuk
        'supplier_name',              // Opsional: Nama supplier
        'notes',                      // Opsional: Catatan tambahan
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'entry_date' => 'date',
    ];

    /**
     * Get the product variant that this stock entry belongs to.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the user who made this stock entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
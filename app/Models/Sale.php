<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini
use Illuminate\Database\Eloquent\Relations\HasMany;   // Tambahkan ini

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',          // ID penjual yang melakukan transaksi
        'customer_name',
        'sale_date',
        'payment_method',   // Misal: "Cash", "Transfer", "QRIS"
        'payment_status',   // Misal: "Belum Dibayar", "Sudah Dibayar", "Dibatalkan"
        'total_amount_sold', // Total nilai penjualan (berdasarkan harga jual)
        'total_cost_of_goods',// Total nilai modal (berdasarkan harga beli/pokok)
        'total_revenue',     // Total keuntungan (total_amount_sold - total_cost_of_goods)
        'notes',             // Opsional: Catatan untuk transaksi
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sale_date' => 'date',
    ];

    /**
     * Get the user (seller) who processed this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the items for the Sale.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Kita bisa tambahkan accessor untuk menghitung total_revenue jika tidak disimpan langsung di DB
    // public function getTotalRevenueAttribute()
    // {
    //     return $this->total_amount_sold - $this->total_cost_of_goods;
    // }
}
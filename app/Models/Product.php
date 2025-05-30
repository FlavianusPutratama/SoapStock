<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by_id', // ID user yang membuat produk
        'updated_by_id', // ID user yang terakhir mengubah produk
    ];

    /**
     * Get all of the variants for the Product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the user who created the product.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the user who last updated the product.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
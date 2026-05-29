<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Inventory
 * 
 * Merepresentasikan data inventaris atau barang yang tersedia untuk dipinjam.
 */
class Inventory extends Model
{
    protected $table = 'inventory';

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'code', 'name', 'category', 'type',
        'total_qty', 'available_qty', 'min_stock',
        'condition', 'notes',
    ];

    /**
     * Casting tipe data agar nilai quantity dan stock selalu integer.
     */
    protected $casts = [
        'total_qty'     => 'integer',
        'available_qty' => 'integer',
        'min_stock'     => 'integer',
    ];

    /**
     * Relasi One-to-Many ke tabel transaction_details.
     * Mendapatkan semua riwayat transaksi yang melibatkan barang ini.
     */
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Relasi One-to-Many ke tabel damaged_items.
     * Mendapatkan riwayat kerusakan untuk barang ini.
     */
    public function damagedItems()
    {
        return $this->hasMany(DamagedItem::class);
    }

    /**
     * Memeriksa apakah stok barang saat ini berada di bawah atau sama dengan batas minimum.
     * 
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->available_qty <= $this->min_stock;
    }
}

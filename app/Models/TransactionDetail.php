<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model TransactionDetail
 * 
 * Merepresentasikan detail barang spesifik yang ada dalam sebuah transaksi peminjaman.
 */
class TransactionDetail extends Model
{
    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'transaction_id', 'inventory_id', 'item_name', 'item_code',
        'item_type', 'qty', 'status', 'qty_returned', 'return_date',
    ];

    /**
     * Casting tipe data untuk tanggal dan angka.
     */
    protected $casts = [
        'qty'          => 'integer',
        'qty_returned' => 'integer',
        'return_date'  => 'datetime',
    ];

    /**
     * Relasi Many-to-One ke tabel transactions.
     * Mendapatkan data transaksi induk.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi Many-to-One ke tabel inventory.
     * Mendapatkan data barang dari inventaris.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Relasi One-to-Many ke tabel returns.
     * Mendapatkan riwayat pengembalian untuk item detail ini.
     */
    public function returns()
    {
        return $this->hasMany(ItemReturn::class, 'transaction_detail_id');
    }
}
